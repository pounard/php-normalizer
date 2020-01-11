<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Type cache abstract implementation.
 */
class TypeDefinitionMapChain implements TypeDefinitionMap
{
    use WithBlacklistTypeDefinitionMap;

    /** @var string[] */
    private $aliases = [];

    /** @var TypeDefinition[] */
    private $cache = [];

    /** @var TypeDefinitionMap[] */
    private $reflectors = [];

    /**
     * Default constructor
     *
     * @param iterable|TypeDefinitionMap[] $reflectors
     */
    public function __construct(iterable $reflectors)
    {
        foreach ($reflectors as $reflector) {
            \assert($reflector instanceof TypeDefinitionMap);
            $this->reflectors[] = $reflector;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $name): bool
    {
        return !$this->isBlacklisted($name) && $this->getNativeType($name);
    }

    /**
     * Real implementation of getNativeType();
     */
    private function doGetNativeType(string $name): string
    {
        foreach ($this->reflectors as $instance) {
            // Per default, most reflectors will just return name, allow
            // each one of them to bypass the default name, else we would
            // have false positives.
            if ($name !== ($nativeType = $instance->getNativeType($name))) {
                return $nativeType;
            }
        }
        return $nativeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(string $name): string
    {
        if (isset($this->cache[$name])) {
            return $name;
        }

        return $this->aliases[$name] ?? (
            $this->aliases[$name] = $this->doGetNativeType($name)
        );
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

        $nativeType = $this->getNativeType($name);

        $type = $this->cache[$nativeType] ?? (
            $this->cache[$nativeType] = $this->doGet($nativeType)
        );

        return $type;
    }
}

/**
 * In-memory type definition cache.
 */
final class CacheItemPoolTypeDefinitionMapCache implements TypeDefinitionMap
{
    /** @var CacheItemPoolInterface */
    private $pool;

    /** @var TypeDefinitionMap */
    private $decorated;

    /** @var TypeDefinition[] */
    private $cache = [];

    /**
     * Default constructor
     *
     * @param iterable|TypeDefinitionMap[] $reflectors
     */
    public function __construct(TypeDefinitionMap $decorated, CacheItemPoolInterface $pool)
    {
        $this->decorated = $decorated;
        $this->pool = $pool;
    }

    /**
     * Does type exist.
     */
    public function exists(string $name): bool
    {
        return $this->decorated->exists($name);
    }

    /**
     * Get type definition.
     *
     * User given type aliases overrides native defined types with
     * the same name. On the other hand, aliases that point to real
     * type name do give you the otherwise conflicting type.
     */
    public function get(string $name): TypeDefinition
    {
        $nativeType = $this->getNativeType($name);

        if (isset($this->cache[$nativeType])) {
            return $this->cache[$nativeType];
        }

        $item = $this->pool->getItem('php_normalizer.'.\str_replace('\\', '.', $nativeType));

        if (!$item->isHit()) {
            $this->pool->save(
                $item->set(
                    $this->decorated->get($nativeType)
                )
            );
        }

        return $item->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(string $name): string
    {
        return $this->decorated->getNativeType($name);
    }
}
