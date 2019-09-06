<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Property definition, it must be serializable.
 */
interface PropertyDefinition
{
    /**
     * Get property native name
     */
    public function getNativeName(): string;

    /**
     * Get property normalized name
     */
    public function getNormalizedName(): string;

    /**
     * @return string[]
     */
    public function getAliases(): array;

    /**
     * @return string[]
     */
    public function getCandidateNames(): array;

    /**
     * @return string[]
     */
    public function getGroups(): array;

    /**
     * Get owner type name
     */
    public function getOwnerType(): string;

    /**
     * May return a PHP type, a normalized type name, or a type alias
     */
    public function getTypeName(): string;

    /**
     * Is value optional
     */
    public function isOptional(): bool;

    /**
     * Is this property a collection
     */
    public function isCollection(): bool;

    /**
     * Get property collection type.
     */
    public function getCollectionType(): ?string;

    /**
     * Clone the instance with the given definition overrides
     *
     * Available options are the same as defined in the yaml file configuration:
     *   - aliases: string[] (default: string[])
     *   - collection: bool (default: false)
     *   - collection_type: string (default: array)
     *   - groups: string[] (default: [])
     *   - normalized_name: ?string (default: null)
     *   - optional: bool (default: false)
     *   - type: string (default: "null")
     *
     * Any null value will be ignored. 'aliases' and 'groups' will only append
     * new values to existing, it will not remove already set ones.
     */
    public function with(array $overrides): PropertyDefinition;

    /**
     * Get array definition, compatible with with() method.
     */
    public function toArray(): array;
}

/**
 * Type definition
 */
interface TypeDefinition
{
    /**
     * Get type native name
     */
    public function getNativeName(): string;

    /**
     * Get type normalized name
     */
    public function getNormalizedName(): string;

    /**
     * Is this type terminal, meaning it does not have children
     */
    public function isTerminal(): bool;

    /**
     * @return PropertyDefinition[]
     */
    public function getProperties(): array;

    /**
     * Clone the instance with the given definition overrides
     *
     * Available options are the same as defined in the yaml file configuration:
     *   - normalized_name: ?string (default: null)
     *   - properties: array[]
     *
     * 'properties' array keys must be native PHP property names, values then
     * are arrays as defined in:
     *
     * @see \MakinaCorpus\Normalizer\PropertyDefinition::with()
     *   For 'properties' values definition documentation.
     */
    public function with(array $overrides): TypeDefinition;

    /**
     * Get array definition, compatible with with() method.
     */
    public function toArray(): array;
}

/**
 * Type definition map, it must be serializable.
 */
interface TypeDefinitionMap
{
    /**
     * Does type exist
     */
    public function exists(string $name): bool;

    /**
     * Get type definition
     *
     * User given type aliases overrides native defined types with
     * the same name. On the other hand, aliases that point to real
     * type name do give you the otherwise conflicting type.
     */
    public function get(string $name): TypeDefinition;

    /**
     * Get native type for
     */
    public function getNativeType(string $name): string;
}

/**
 * Array based property definition
 */
final class DefaultPropertyDefinition implements PropertyDefinition
{
    /** @var string[] */
    private $aliases;

    /** @var string[] */
    private $candidateNames;

    /** @var bool */
    private $collection = false;

    /** @var ?string */
    private $collectionType;

    /** @var string[] */
    private $groups;

    /** @var string[] */
    private $name;

    /** @var string */
    private $normalizedName;

    /** @var bool */
    private $optional;

    /** @var string */
    private $owner;

    /** @var string */
    private $type;

    /**
     * Default constructor
     */
    public static function fromArray(string $owner, string $name, array $definition)
    {
        $ret = new self;
        $ret->aliases = $definition['aliases'] ?? null;
        $ret->collection = (bool)($definition['collection'] ?? false);
        $ret->collectionType = $definition['collection_type'] ?? null;
        $ret->groups = $definition['groups'] ?? [];
        $ret->name = $name;
        $ret->normalizedName = $definition['normalized_name'] ?? null;
        $ret->optional = (bool)($definition['optional'] ?? false);
        $ret->owner = $owner;
        $ret->type = $definition['type'] ?? 'null';

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeName(): string
    {
        return $this->name;
    }

    public function getNormalizedName(): string
    {
        return $this->normalizedName ?? $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return $this->aliases ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCandidateNames(): array
    {
        return $this->candidateNames ?? (
            $this->candidateNames = \array_unique(
                \array_merge(
                    [$this->name, $this->getNormalizedName()],
                    $this->getAliases()
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * {@inheritdoc}
     */
    public function isCollection(): bool
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerType(): string
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionType(): ?string
    {
        if (!$this->collection) {
            return null;
        }
        return $this->collectionType ?? 'array';
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $overrides): PropertyDefinition
    {
        $ret = clone $this;

        if (isset($overrides['aliases'])) {
            $ret->aliases = \array_unique(\array_merge($ret->aliases ?? [], $overrides['aliases']));
        }
        if (isset($overrides['collection'])) {
            $ret->collection = (bool)$overrides['collection'];
        }
        if (isset($overrides['collection_type'])) {
            $ret->collectionType = $overrides['collection_type'];
        }
        if (isset($overrides['groups'])) {
            $ret->groups = \array_unique(\array_merge($ret->groups, $overrides['groups']));
        }
        if (isset($overrides['normalized_name'])) {
            $ret->normalizedName = $overrides['normalized_name'];
        }
        if (isset($overrides['optional'])) {
            $ret->optional = (bool)$overrides['optional'];
        }
        if (isset($overrides['type']) && 'null' !== $overrides['type']) {
            $ret->type = $overrides['type'];
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'aliases' => $this->aliases,
            'collection' => $this->collection,
            'collection_type' => $this->collectionType,
            'groups' => $this->groups,
            'normalized_name' => $this->normalizedName,
            'optional' => $this->optional,
            'type' => $this->type !== 'null' ? $this->type : null,
        ];
    }
}

/**
 * Array based type definition
 */
final class DefaultTypeDefinition implements TypeDefinition
{
    /** @var string */
    private $name;

    /** @var string */
    private $normalizedName;

    /** @var PropertyDefinition[] */
    private $properties = [];

    /**
     * Default constructor
     */
    public static function fromArray(string $name, array $data)
    {
        $ret = new self;
        $ret->name = $name;
        $ret->normalizedName = $data['normalized_name'] ?? $name;

        foreach ($data['properties'] ?? [] as $key => $value) {
            $ret->properties[$key] = DefaultPropertyDefinition::fromArray($name, $key, $value);
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizedName(): string
    {
        return $this->normalizedName;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function isTerminal(): bool
    {
        return empty($this->properties);
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $overrides): TypeDefinition
    {
        $ret = clone $this;
        $properties = $overrides['properties'] ?? [];

        if (isset($overrides['normalized_name'])) {
            $this->normalizedName = $overrides['normalized_name'];
        }

        // Override (and deep clone) properties at the same time
        foreach (\array_keys($this->properties) as $name) {
            if (isset($ret->properties[$name])) {
                $ret->properties[$name] = $this->properties[$name]->with($properties[$name]);
            }
        }

        // Add potentially missing (newly added) properties
        foreach ($properties as $name => $definition) {
            if (!isset($ret->properties[$name])) {
                $ret->properties[$name] = DefaultPropertyDefinition::fromArray($this->name, $name, $definition);
            }
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'properties' => \array_map(
                static function (PropertyDefinition $property) {
                    return $property->toArray();
                },
                $this->properties
            ),
            'normalized_name' => $this->normalizedName,
        ];
    }
}

/**
 * Blacklist
 */
trait WithBlacklistTypeDefinitionMap
{
    private $blacklist = [];

    /**
     * @param string[] $typeList
     *   PHP class name list
     */
    public function setBlacklist(array $typeList): void
    {
        $this->blacklist = \array_flip($typeList);
    }

    public function isBlacklisted(string $type): bool
    {
        return isset($this->blacklist[$type]);
    }
}

/**
 * Array based type definition map
 */
final class ArrayTypeDefinitionMap implements TypeDefinitionMap
{
    /** @var string[] */
    private $aliases;

    /** @var TypeDefinition[] */
    private $types = [];

    /**
     * Default constructor
     */
    public function __construct(array $data, array $aliases = [])
    {
        $this->aliases = $aliases;

        foreach ($data as $type => $definition) {
            $this->types[$type] = DefaultTypeDefinition::fromArray($type, $definition);
        }
    }

    /**
     * Add type definition, overrides any existing one
     */
    public function addTypeDefinition(string $name, TypeDefinition $type): void
    {
        // This will override any previous definition.
        $this->types[$name] = $type;
    }

    /**
     * Merge user configuration with existing definitions
     */
    public function mergeUserConfiguration(array $data): void
    {
        throw new NotImplementedError();
    }

    /**
     * Raise a type error not found
     */
    private function typeNotFoundError(string $name, ?string $alias)
    {
        if ($alias && $alias !== $name) {
            throw new TypeDoesNotExistError(\sprintf(
                "Alias '%s' maps to non existing type '%s'",
                $alias, $name
            ));
        }
        throw new TypeDoesNotExistError($name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $name): bool
    {
        $key = $this->aliases[$name] ?? $name;

        return isset($this->types[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): TypeDefinition
    {
        $key = $this->aliases[$name] ?? $name;

        return $this->types[$key] ?? $this->typeNotFoundError($key, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(string $name): string
    {
        return $this->aliases[$name] ?? $name;
    }
}
