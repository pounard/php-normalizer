<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * (De)Normalizer facade, glues together external normalizer chain and
 * normalization code generation.
 */
interface Normalizer
{
    /**
     * Dernormalise object
     */
    public function normalize($object, Context $context);

    /**
     * Dernormalise object
     */
    public function denormalize(string $type, $input, Context $context);
}
