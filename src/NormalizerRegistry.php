<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Finds normalizer classes.
 */
interface NormalizerRegistry
{
    /**
     * Find normalizer, include any file if necessary.
     *
     * @return string
     *   Class name, which is a static normalizer
     *   @todo write an interface for those?
     */
    public function find(string $className): ?string;
}

/**
 * Writable normalizer registry.
 */
interface WritableNormalizerRegistry extends NormalizerRegistry
{
    /**
     * Register a new normalizer.
     */
    public function register(string $className, string $normalizerClassName, string $filename, array $dependencies = []): void;

    /**
     * Unregister based upon the target class.
     */
    public function unregisterClass(string $className): void;

    /**
     * Unregistrer based upon the normalizer name.
     */
    public function unregisterNormalizer(string $normalizerClassName): void;
}
