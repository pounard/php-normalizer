<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Type cache abstract implementation.
 */
abstract class TypeDefinitionMapCache implements TypeDefinitionMap
{
    /** @var TypeDefinitionMap[] */
    private $reflectors;

    /** @var ArrayTypeDefinitionMap */
    private $static;

    /** @var bool[] */
    private $existing = [];

    /**
     * Default constructor
     *
     * @param iterable|TypeDefinitionMap[] $reflectors
     */
    public function __construct(iterable $reflectors)
    {
        $this->reflectors = $reflectors;
        $this->static = new ArrayTypeDefinitionMap([]);
        // @todo load cache.
    }

    /**
     * Set static definition map, the one including base configuration
     */
    public function setUserConfiguration(array $userConfiguration): void
    {
        throw new NotImplementedError();
    }

    /**
     * Set type aliases
     */
    public function setUserAliases(array $aliases): void
    {
        throw new NotImplementedError();
    }

    /**
     * exists() implementation
     */
    private function doExists(string $name): bool
    {
        if ($this->static->exists($name)) {
            return true;
        }
        foreach ($this->reflectors as $instance) {
            if ($instance->exists($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $name): bool
    {
        return $this->existing[$name] ?? ($this->existing[$name] = $this->doExists($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(string $name): string
    {
        // Considering that specific implementations should not carry
        // user configuration, we skip this for reflectors, user configuration
        // aliases and such is to be overriden by the one set in this object.
        return $this->static->getNativeType($name);
    }

    /**
     * get() implementation
     */
    private function doGet(string $name): TypeDefinition
    {
        // Else find it with reflectors and propagate it into the static array
        // based memory map, so we won't attempt anymore reflection at runtime.
        foreach ($this->reflectors as $instance) {
            if ($instance->exists($name)) {
                try {
                    return $instance->get($name);
                } catch (TypeDoesNotExistError $e) {
                    continue;
                }
            }
        }
        throw new TypeDoesNotExistError(\sprintf("Could not find type '%s'", $name));
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): TypeDefinition
    {
        if (!$this->exists($name)) {
            throw new TypeDoesNotExistError(\sprintf("Could not find type '%s'", $name));
        }

        if ($this->static->exists($name)) {
            return $this->static->get($name);
        }

        $nativeType = $this->static->getNativeType($name);
        $this->static->addTypeDefinition($nativeType, $type = $this->doGet($nativeType));

        // New types are added, add them to cache.
        // @todo

        return $type;
    }
}

/**
 * In-memory type definition cache.
 */
final class MemoryTypeDefinitionMapCache extends TypeDefinitionMapCache
{
}
