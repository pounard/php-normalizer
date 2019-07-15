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
        $ret->aliases = $definition['aliases'] ?? [];
        $ret->collection = (bool)($definition['collection'] ?? false);
        $ret->collectionType = $definition['collection_type'] ?? null;
        $ret->groups = $definition['groups'] ?? [];
        $ret->name = $name;
        $ret->normalizedName = $definition['normalized_name'] ?? $ret->name;
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
        return $this->normalizedName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function getCandidateNames(): array
    {
        return $this->candidateNames ?? (
            $this->candidateNames = \array_merge(
                [$this->name, $this->getNormalizedName()],
                $this->getAliases()
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
        throw new TypeDoesNotExistError(\sprintf("Type '%s' does not exist", $name));
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $name): bool
    {
        return isset($this->types[$name]) || isset($this->types[$name]);
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
