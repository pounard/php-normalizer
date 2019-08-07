<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

/**
 * This implementation does not support aliases, it should only be used
 * chained with a more complete or cached implementation when dynamic
 * type information lookup is required.
 */
class ReflectionTypeDefinitionMap implements TypeDefinitionMap
{
    private $typeInfoExtractor;
    private $typeInfoExtractorLoaded = false;

    /**
     * Attempt to create a type info extractor if Symfony component is enabled
     */
    public static function createDefaultTypeInfoExtractor(): ?PropertyTypeExtractorInterface
    {
        if (\interface_exists(PropertyTypeExtractorInterface::class)) {
            $reflectionExtractor = new ReflectionExtractor();
            $phpDocExtractor = new PhpDocExtractor();

            return new PropertyInfoExtractor(
                [],
                [$phpDocExtractor, $reflectionExtractor],
                [$phpDocExtractor, $reflectionExtractor],
                [$reflectionExtractor],
                [$reflectionExtractor]
            );
        }
        return null;
    }

    /**
     * Set type info extractor
     */
    public function setTypeInfoExtractor(PropertyTypeExtractorInterface $typeInfoExtractor): void
    {
        $this->typeInfoExtractorLoaded = true;
        $this->typeInfoExtractor = $typeInfoExtractor;
    }

    /**
     * Parse property definition
     *
     * Properties types are much harder to get than class details, since
     * PHP doesn't allow properties to be typed. We can only rely upon
     * default value, or annoted types in code documentation.
     *
     * For this, we don't have many other choice than using the
     * symfony/property-info component, that does the job pretty well.
     */
    private function findPropertyDefinition(string $class, \ReflectionProperty $property): array
    {
        if (!$this->typeInfoExtractorLoaded && !$this->typeInfoExtractor) {
            $this->typeInfoExtractorLoaded = true;
            $this->typeInfoExtractor = self::createDefaultTypeInfoExtractor();
        }

        if ($this->typeInfoExtractor) {
            $types = $this->typeInfoExtractor->getTypes($class, $property->getName());

            if ($types) {
                /** @var \Symfony\Component\PropertyInfo\Type $type */
                foreach ($types as $type) {
                    if ($type->isCollection()) {
                        $valueType = $type->getCollectionValueType();
                        return [
                            'type' => $valueType->getClassName() ?? $valueType->getBuiltinType(),
                            'optional' => $type->isNullable(),
                            'collection' => true,
                            'collection_type' => $type->getClassName() ?? $type->getBuiltinType(),
                        ];
                    }
                    return [
                        'type' => $type->getClassName() ?? $type->getBuiltinType(),
                        'optional' => $type->isNullable(),
                        'collection' => false,
                        'collection_type' => $type->getBuiltinType(),
                    ];
                }
            }
        }

        return [
            'type' => null,
            'optional' => true,
            'collection' => false,
        ];
    }

    /**
     * Recursion to find all parent and traits included properties.
     */
    private function findAllProperties(?\ReflectionClass $class)
    {
        if (null === $class) {
            return [];
        }
        return \array_values(\array_merge(
            $this->findAllProperties($class->getParentClass() ?: null),
            \array_values(\array_filter(
                $class->getProperties(),
                function (\ReflectionProperty $property) : bool {
                    return !$property->isStatic();
                }
            ))
        ));
    }

    /**
     * Parse class definition
     */
    private function findClassDefinition(string $class): TypeDefinition
    {
        $ref = new \ReflectionClass($class);
        $data = [];

        /** @var \ReflectionProperty $propDef */
        foreach ($this->findAllProperties($ref) as $propDef) {
            $data['properties'][$propDef->getName()] = $this->findPropertyDefinition($class, $propDef);
        }

        return DefaultTypeDefinition::fromArray($class, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $name): bool
    {
        return \class_exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): TypeDefinition
    {
        if (!\class_exists($name)) {
            throw new ClassDoesNotExistError($name);
        }

        return $this->findClassDefinition($name);
    }

    /**
     * Get native type for
     */
    public function getNativeType(string $name): string
    {
        return $name;
    }
}
