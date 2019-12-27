<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

/**
 * Generator runtime interface. Allows anyone to replace the generator
 * implementation transparently.
 */
interface Generator
{
    /**
     * Get normalizer class name
     *
     * It can return null if the class does not exist and the generator is not
     * able to generate the normalizer.
     */
    public function getNormalizerClass(string $className): ?string;

    /**
     * Get normalizer class
     *
     * @return string
     *   Generated normalizer class name
     */
    public function generateNormalizerClass(string $className): string;
}
