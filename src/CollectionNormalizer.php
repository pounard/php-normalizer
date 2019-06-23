<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Scalar types normalizer and denormalizer
 */
final class CollectionNormalizer implements Normalizer, Denormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        return $this->handleValue($type, $object, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        return $this->handleValue($type, $data, $context);
    }
}
