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
    /** @var bool */
    private $propertiesAreTyped = false;

    /** @var ?PropertyTypeExtractorInterface */
    private $typeInfoExtractor;

    /** @var bool */
    private $typeInfoExtractorLoaded = false;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->propertiesAreTyped = (\version_compare(PHP_VERSION, '7.4.0') >= 0);
    }

    /**
     * Attempt to create a type info extractor if Symfony component is present.
     *
     * This can return null if the dependency was not installed, hence the
     * self::$typeInfoExtractorLoaded boolean to avoid redundant class and
     * interface existence checks.
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
     * Is type supported by this normalizer API.
     */
    public static function isTypeSupported(string $type): bool
    {
        return 'resource' !== $type && 'callable' !== $type && (\interface_exists($type) || \class_exists($type));
    }

    /**
     * @internal For unit testing purpose only.
     */
    public function disablePropertyTypeReflection(): void
    {
        $this->propertiesAreTyped = false;
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
     * Find property type using PHP 7.4+ reflection and property type.
     */
    private function findPropertyWithReflection(string $class, \ReflectionProperty $property): ?array
    {
        if (!$this->propertiesAreTyped || !$property->hasType()) {
            return null;
        }

        $refType = $property->getType();
        $typeName = $refType->getName();

        if (!self::isTypeSupported($typeName)) {
            return null;
        }

        if (!$refType->isBuiltIn()) {
            // If it's not built-in, it's a class or interface.
            return [
                'collection' => false,
                'optional' => $refType->allowsNull(),
                'type' => $typeName,
            ];
        }

        if ('array' === $typeName || 'iterable' === $typeName) {
            // We cannot have the real value type, just let this pass and
            // proceed with other property reflections, such as doc block
            // or property info component.
            return null;
        }

        // All other types, we already ignored 'callable' and 'resource'
        // types upper, so everything remaining here are scalar types.
        return [
            'collection' => false,
            'optional' => $refType->allowsNull(),
            'type' => $typeName,
        ];
    }

    /**
     * Find property type using symfony/property-info.
     */
    private function findPropertyWithPropertyInfo(string $class, \ReflectionProperty $property): ?array
    {
        if (!$typeInfoExtractor = $this->getTypeInfoExtractor()) {
            return null;
        }
        if (!$types = $typeInfoExtractor->getTypes($class, $property->getName())) {
            return null;
        }

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
    private function findPropertyDefinition(string $class, \ReflectionProperty $property): ?array
    {
        if ($ret = $this->findPropertyWithReflection($class, $property)) {
            return $ret;
        }
        // @todo write here a custom docblock parser for speed.
        if ($ret = $this->findPropertyWithPropertyInfo($class, $property)) {
            return $ret;
        }

        // Default is to propagate a 'null' type, which means allow anything
        // hydration without type validation or normalization.
        return [
            'collection' => false,
            'optional' => true,
            'type' => 'null',
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
        // Recursive algorithm that lookup into the whole class hierarchy.
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
            // Properties can be ignored (eg. 'resource' or 'callable').
            if ($def = $this->findPropertyDefinition($class, $propDef)) {
                $def['declared_scope'] = $propDef->isProtected() ? 'protected' : ($propDef->isPrivate() ? 'private' : 'public');
                $def['declaring_class'] = $propDef->getDeclaringClass()->name;
                $data['properties'][$propDef->getName()] = $def;
            }
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

        if (\interface_exists($name)) {
            throw new RuntimeError(\sprintf("'%s': interfaces cannot be (de)normalized", $name));
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
