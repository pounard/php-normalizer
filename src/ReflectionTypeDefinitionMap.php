<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

/**
 * This implementation does not support aliases, it should only be used
 * chained with a more complete or cached implementation when dynamic
 * type information lookup is required.
 */
class ReflectionTypeDefinitionMap implements TypeDefinitionMap
{
    private $typeInfoExtractor;

    /**
     * Set type info extractor
     */
    public function setTypeInfoExtractor(PropertyTypeExtractorInterface $typeInfoExtractor): void
    {
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
     * Parse class definition
     */
    private function findClassDefinition(string $class): TypeDefinition
    {
        $ref = new \ReflectionClass($class);
        $data = [];

        /** @var \ReflectionProperty $propDef */
        foreach ($ref->getProperties() as $propDef) {
            if (!$propDef->isStatic()) {
                $data['properties'][$propDef->getName()] = $this->findPropertyDefinition($class, $propDef);
            }
        }

        return new ArrayTypeDefinition($class, $data);
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
            throw new TypeDoesNotExistError(\sprintf("Class '%s' does not exist", $name));
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
