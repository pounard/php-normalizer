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
    }

    /**
     * Load type from cache
     */
    protected function loadFromCache(string $name): ?TypeDefinition
    {
        return null;
    }

    /**
     * Store into cache
     */
    protected function storeIntoCache(string $name, TypeDefinition $type): void
    {
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
        /** @var \MakinaCorpus\Normalizer\TypeDefinition $type */
        $type = null;

        // Else find it with reflectors and propagate it into the static array
        // based memory map, so we won't attempt anymore reflection at runtime.
        foreach ($this->reflectors as $instance) {
            if ($instance->exists($name)) {
                try {
                    if ($type) {
                        $type = $type->with($instance->get($name)->toArray());
                    } else {
                        $type = $instance->get($name);
                    }
                } catch (TypeDoesNotExistError $e) {
                    continue;
                }
            }
        }

        if ($type) {
            return $type;
        }
        throw new TypeDoesNotExistError($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): TypeDefinition
    {
        if (!$this->exists($name)) {
            throw new TypeDoesNotExistError($name);
        }

        if ($this->static->exists($name)) {
            return $this->static->get($name);
        }

        $nativeType = $this->static->getNativeType($name);

        if (!$type = $this->loadFromCache($nativeType)) {
            $type = $this->doGet($nativeType);
        }

        // New types are added, add them to memory and to cache.
        $this->static->addTypeDefinition($nativeType, $type);
        $this->storeIntoCache($name, $type);

        return $type;
    }
}

/**
 * In-memory type definition cache.
 */
final class MemoryTypeDefinitionMapCache extends TypeDefinitionMapCache
{
}
