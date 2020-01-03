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
        return \in_array($type, ['bool', 'float', 'int', 'null', 'string', 'array']) || \class_exists($type) || \interface_exists($type);
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
     * From a class name, resolve a class alias.
     *
     * Basically, what we want to achieve here, is to resolve a use statement.
     */
    private static function resolveTypeFromClass(string $class, string $type, bool $allowUnsafeClassResolution = false): ?string
    {
        $class = new \ReflectionClass($class);

        if ($type === $class->getShortName()) {
            return $class->getName();
        }

        if ($allowUnsafeClassResolution) {
            if ($namespace = $class->getNamespaceName()) {
                // This is wrong because we don't have file use statements.
                // Local classes could be aliased and hidden.
                $candidate = '\\'.$namespace.'\\'.$type;
                if (\class_exists($candidate) || \interface_exists($candidate)) {
                    return $candidate;
                }
            }
        }

        return null;

        /*
        if (!$filename = $class->getFileName()) {
            return null;
        }

        // Resolver::resolveUseStatements($filename);
         */

        return null;
    }

    /**
     * From a reflection property, resolve a class alias.
     *
     * Basically, what we want to achieve here, is to resolve a use statement.
     */
    public static function resolveTypeFromClassProperty(string $class, \ReflectionProperty $property, string $type, bool $allowUnsafeClassResolution = false): ?string
    {
        if (!$type) {
            return null; // Empty type.
        }
        if ('\\' === $type[0]) {
            return $type; // FQDN
        }
        return self::resolveTypeFromClass($class, $type, $allowUnsafeClassResolution);
    }

    /**
     * Return an array of type definition arrays from an arbitrary doc block
     */
    public static function extractTypesFromDocBlock(string $docBlock): ?array
    {
        // This is where it becomes really ulgy.
        $matches = [];
        if (!\preg_match('/@var\s+([^\s\n@]+)/ums', $docBlock, $matches)) {
            return null;
        }

        $typeStrings = \array_unique(
            \array_filter(
                \array_map(
                    '\trim',
                    \explode('|', $matches[1])
                )
            )
        );

        // If one occurence of 'null' or an unsupported type is found, we can
        // consider the whole as optional, because we will not be able to
        // normalize some variants of it.
        $allAreOptional = false;
        foreach ($typeStrings as $index => $type) {
            if ('null' === $type || 'callable' === $type || 'resource' === $type) {
                unset($typeStrings[$index]);
                $allAreOptional = true;
            }
        }

        $ret = [];
        foreach ($typeStrings as $type) {
            if ($optional = '?' === $type[0]) {
                $type = \substr($type, 1);
            }
            if ($collection = '[]' === \substr($type, -2)) {
                $type = \substr($type, 0, -2);
            }

            // Proceed to a second removal pass now that '[]' and '?' have
            // been stripped.
            if (!self::isTypeSupported($type)) {
                continue;
            }

            // Internal type reprensentation with uses a QDN which must be
            // absolute, and unprefixed with '\\'. Else custom class resolver
            // will fail when using CLASS::class constant.
            $type = \trim($type, '\\');

            $ret[] = [
                'collection' => $collection,
                'collection_type' => $type,
                'optional' => $optional || $allAreOptional,
                'type' => $type,
            ];
        }

        return $ret;
    }

    /**
     * Find property type with raw doc block from reflexion
     */
    private function findPropertyWithRawDocBlock(string $class, \ReflectionProperty $property): ?array
    {
        if (!$docBlock = $property->getDocComment()) {
            return null;
        }

        // Arbitrary take the first, sorry.
        // We don't support union types yet.
        foreach (self::extractTypesFromDocBlock($docBlock) as $array) {
            if ($realType = self::resolveTypeFromClass($class, $array['type'])) {
                $array['type'] = $realType;
            }
        }

        return null;
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
            $typeName = $type->getClassName() ?? $type->getBuiltinType();
            if ($type->isCollection()) {
                $valueType = $type->getCollectionValueType();
                return [
                    'collection' => true,
                    'collection_type' => ($valueType ? $typeName : 'null'),
                    'optional' => $type->isNullable(),
                    'type' => ($valueType ? ($valueType->getClassName() ?? $valueType->getBuiltinType()) : 'null'),
                ];
            }
            return [
                'collection' => false,
                'collection_type' => $type->getBuiltinType(),
                'optional' => $type->isNullable(),
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
    private function findPropertyDefinition(string $class, \ReflectionProperty $property): ?array
    {
        if ($ret = $this->findPropertyWithReflection($class, $property)) {
            return $ret;
        }
        if ($ret = $this->findPropertyWithRawDocBlock($class, $property)) {
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
