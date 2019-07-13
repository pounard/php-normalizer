<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Normalizer
 */
interface Normalizer
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

/**
 * Denormalizer
 */
interface Denormalizer
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

/**
 * Helper for writing shorter custom normalizer and denormalizer
 */
trait CustomNormalizerTrait
{
    /**
     * Declare normalized and denormalized types
     */
    abstract protected function getHandledTypes(): string;

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return \in_array($type, $this->getHandledTypes());
    }
}

/**
 * Helper for writing shorter custom normalizer and denormalizer
 */
trait CustomDenormalizerTrait
{
    /**
     * Declare normalized and denormalized types
     */
    abstract protected function getHandledTypes(): string;
    
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return \in_array($type, $this->getHandledTypes());
    }
}
