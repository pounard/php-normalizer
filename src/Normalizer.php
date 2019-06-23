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
 * Normalizers implementing this allow performance implementation during
 * normalizer lookup at runtime.
 */
interface DeclarativeNormalizer extends Normalizer
{
    public function getNormalizedTypes(): array;
}

/**
 * Denormalizers implementing this allow performance implementation during
 * denormalizer lookup at runtime.
 */
interface DeclarativeDenormalizer extends Denormalizer
{
    public function getDenormalizedTypes(): array;
}

/**
 * Base implementation for instances that implement both DeclarativeNormalizer
 * and DeclarativeDenormalizer at the same time.
 */
trait BothDeclarativeNormalizer
{
    /**
     * Declare normalized and denormalized types
     */
    abstract protected function getHandledTypes(): string;

    /**
     * {@inheritdoc}
     */
    public function getNormalizedTypes(): array
    {
        return $this->getHandledTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return \in_array($type, $this->getHandledTypes());
    }

    /**
     * {@inheritdoc}
     */
    public function getDenormalizedTypes(): array
    {
        return $this->getHandledTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return \in_array($type, $this->getHandledTypes());
    }
}
