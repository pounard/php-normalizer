<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Normalizer;

use MakinaCorpus\Normalizer\Context;

/**
 * Denormalizer
 */
interface CustomDenormalizer
{
    /**
     * Denormalize array of values to the given type
     *
     * Data can either be an array, or any other scalar type (int, string, ..)
     */
    public function denormalize(string $type, $data, Context $context);

    /**
     * Can this denormalizer denormalize this type
     */
    public function supportsDenormalization(string $type): bool;
}
