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
final class ReflectionTypeDefinitionMap implements TypeDefinitionMap
{
    private $propertiesAreTyped = false;
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
     * Default constructor.
     */
    public function __construct()
    {
        $this->propertiesAreTyped = (\version_compare(PHP_VERSION, '7.4.0') >= 0);
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
     * Get type info extractor
     */
    protected function getTypeInfoExtractor(): PropertyTypeExtractorInterface
    {
        if (!$this->typeInfoExtractorLoaded && !$this->typeInfoExtractor) {
            $this->typeInfoExtractorLoaded = true;
            $this->typeInfoExtractor = self::createDefaultTypeInfoExtractor();
        }

        return $this->typeInfoExtractor;
    }

    /**
     * Attempt using only reflection (no property-info).
     */
    private function findPropertyWithReflection(string $class, \ReflectionProperty $property): ?array
    {
        // Attempt using typed properties, thus avoiding to use the property-info
        // component, which is really slow.
        if (!$property->hasType()) {
            return null;
        }

        $refType = $property->getType();
        $typeName = $refType->getName();

        // If it's not builtin, it's a class, and that's great for us.
        if (!$refType->isBuiltIn()) {
            return [
                'collection' => false,
                'optional' => $refType->allowsNull(),
                'type' => $typeName,
            ];
        }

        switch ($typeName) {

            case 'array':
            case 'iterable':
                // We cannot have the real value type, just let this pass and
                // proceed with property info.
                return null;

            case 'callable':
            case 'object':
            case 'resource':
                // All those types are not and will not be supported
                // by this hydrator, so just let it return a 'null'
                // type, which will disable all validations.
                return [
                    'collection' => false,
                    'optional' => true,
                    'type' => 'null', // Ignore this type.
                ];

            default:
                // OK this is all the internal types we support (scalars pretty much).
                return [
                    'collection' => false,
                    'optional' => $refType->allowsNull(),
                    'type' => $typeName,
                ];
        }
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
        if ($this->propertiesAreTyped) {
            if ($ret = $this->findPropertyWithReflection($class, $property)) {
                return $ret;
            }
        }

        $typeInfoExtractor = $this->getTypeInfoExtractor();

        if ($typeInfoExtractor) {
            $types = $typeInfoExtractor->getTypes($class, $property->getName());

            if ($types) {
                /** @var \Symfony\Component\PropertyInfo\Type $type */
                foreach ($types as $type) {
                    if ($type->isCollection()) {
                        $valueType = $type->getCollectionValueType();
                        return [
                            'collection' => true,
                            'collection_type' => $type->getClassName() ?? $type->getBuiltinType(),
                            'optional' => $type->isNullable(),
                            'type' => $valueType->getClassName() ?? $valueType->getBuiltinType(),
                        ];
                    }
                    return [
                        'collection' => false,
                        'collection_type' => $type->getBuiltinType(),
                        'optional' => $type->isNullable(),
                        'type' => $type->getClassName() ?? $type->getBuiltinType(),
                    ];
                }
            }
        }

        return [
            'collection' => false,
            'optional' => true,
            'type' => null,
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
            $def = $this->findPropertyDefinition($class, $propDef);
            $def['declared_scope'] = $propDef->isProtected() ? 'protected' : ($propDef->isPrivate() ? 'private' : 'public');
            $def['declaring_class'] = $propDef->getDeclaringClass()->name;
            $data['properties'][$propDef->getName()] = $def;
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
        // Handle gracefully native PHP native types.
        switch ($name) {
            case 'array':
            case 'bool':
            case 'float':
            case 'int':
            case 'null':
            case 'resource':
            case 'string':
                return DefaultTypeDefinition::simple($name);
        }

        if (!\class_exists($name)) {
            throw new ClassDoesNotExistError($name);
        }

        return $this->findClassDefinition($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(string $name): string
    {
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAliases(): array
    {
        return [];
    }
}
