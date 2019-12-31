<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Normalizer;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\UnsupportedTypeError;

/**
 * (De)Normalizer chain facade.
 */
final class CustomNormalizerChain implements CustomNormalizer, CustomDenormalizer
{
    private $denormalizers = [];
    private $denormalizersCache = [];
    private $normalizers = [];
    private $normalizersCache = [];

    /**
     * @param CustomNormalizer[]|CustomDenormalizer[] $implementations
     */
    public function __construct(iterable $implementations = [])
    {
        foreach ($implementations as $instance) {
            $this->register($instance);
        }
    }

    /**
     * Register a single (de)normalizer
     *
     * @param CustomNormalizer|CustomDenormalizer $instance
     */
    public function register($instance)
    {
        if ($instance instanceof CustomDenormalizer) {
            $this->denormalizers[] = $instance;
        }
        if ($instance instanceof CustomNormalizer) {
            $this->normalizers[] = $instance;
        }
    }

    /**
     * Find denormalizer for type
     */
    private function findDenormalizer(string $type): ?CustomDenormalizer
    {
        if (\array_key_exists($type, $this->denormalizersCache)) {
            return $this->denormalizersCache[$type];
        }
        /** @var \MakinaCorpus\Normalizer\Denormalizer $instance */
        foreach ($this->denormalizers as $instance) {
            if ($instance->supportsDenormalization($type)) {
                return $this->denormalizersCache[$type] = $instance;
            }
        }
        return $this->denormalizersCache[$type] = null;
    }

    /**
     * Find normalizer for type
     */
    private function findNormalizer(string $type): ?CustomNormalizer
    {
        if (\array_key_exists($type, $this->normalizersCache)) {
            return $this->normalizersCache[$type];
        }
        /** @var \MakinaCorpus\Normalizer\Normalizer $instance */
        foreach ($this->normalizers as $instance) {
            if ($instance->supportsNormalization($type)) {
                return $this->normalizersCache[$type] = $instance;
            }
        }
        return $this->normalizersCache[$type] = null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return null !== $this->findNormalizer($type);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return null !== $this->findDenormalizer($type);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        if ($denormalizer = $this->findDenormalizer($type)) {
            return $denormalizer->denormalize($type, $data, $context);
        }
        throw new UnsupportedTypeError($type);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        if ($normalizer = $this->findNormalizer($type)) {
            return $normalizer->normalize($type, $object, $context);
        }
        throw new UnsupportedTypeError($type);
    }
}
