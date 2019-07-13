<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Property definition
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
 * Type definition map
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
final class ArrayPropertyDefinition implements PropertyDefinition
{
    private $candidateNames;
    private $data;
    private $name;
    private $owner;

    /**
     * Default constructor
     */
    public function __construct(string $owner, string $name, array $data)
    {
        $this->data = $data;
        $this->name = $name;
        $this->owner = $owner;
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
        return $this->data['normalized_name'] ?? $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return $this->data['aliases'] ?? [];
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
        return (bool)($this->data['optional'] ?? false);
    }

    /**
     * {@inheritdoc}
     */
    public function isCollection(): bool
    {
        return (bool)($this->data['collection'] ?? false);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName(): string
    {
        return $this->data['type'] ?? 'null';
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
        return $this->data['groups'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionType(): ?string
    {
        if (!$this->isCollection()) {
            return null;
        }
        return $this->data['collection_type'] ?? 'array';
    }
}

/**
 * Array based type definition
 */
final class ArrayTypeDefinition implements TypeDefinition
{
    private $data;
    private $name;
    private $properties;

    /**
     * Default constructor
     */
    public function __construct(string $name, array $data)
    {
        $this->data = $data;
        $this->name = $name;
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
        return $this->data['normalized_name'] ?? $this->name;
    }

    /**
     * Create property array
     */
    private function createPropertyArray(): array
    {
        $ret = [];

        // We cannot use \array_map() because we need key to propagate
        // to the instance we create in there.
        foreach ($this->data['properties'] ?? [] as $key => $value) {
            $ret[$key] = new ArrayPropertyDefinition($this->name, $key, $value);
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties(): array
    {
        return $this->properties ?? ($this->properties = $this->createPropertyArray());
    }

    /**
     * {@inheritdoc}
     */
    public function isTerminal(): bool
    {
        return empty($this->data['properties']);
    }
}

/**
 * Array based type definition map
 */
final class ArrayTypeDefinitionMap implements TypeDefinitionMap
{
    private $aliases;
    private $cache = [];
    private $types;

    /**
     * Default constructor
     */
    public function __construct(array $types, array $aliases = [])
    {
        $this->aliases = $aliases;
        $this->types = $types;
    }

    /**
     * Raise a type error not found
     */
    private function typeNotFoundError(string $name, ?string $alias)
    {
        if ($alias && $alias !== $name) {
            throw new TypeDoesNotExistError(\sprintf(
                "Type '%s' maps to non existing type '%s'",
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
        return isset($this->types[$name]) || isset($this->aliases[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): TypeDefinition
    {
        // Aliases override definitions
        $key = $this->aliases[$name] ?? $name;

        return $this->cache[$key] ?? (
            $this->cache[$key] = new ArrayTypeDefinition(
                $key,
                $this->types[$key] ?? $this->typeNotFoundError($key, $name)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(string $name): string
    {
        return $this->aliases[$name] ?? $name;
    }
}

