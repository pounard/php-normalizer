<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Normalizer;

use MakinaCorpus\Normalizer\Context;

/**
 * Normalizer
 */
interface CustomNormalizer
{
    /**
     * Normalize object or value of the given type
     */
    public function normalize(string $type, $object, Context $context);

    /**
     * Can this normalizer normalize this type
     */
    public function supportsNormalization(string $type): bool;
}
