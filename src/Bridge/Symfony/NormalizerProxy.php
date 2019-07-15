<?php

namespace MakinaCorpus\Normalizer\Bridge\Symfony;

use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Passthrough for Symfony serializer
 */
final class NormalizerProxy implements NormalizerInterface, DenormalizerInterface
{
    private $contextFactory;
    private $normalizer;

    /**
     * Default constructor
     */
    public function __construct(ContextFactory $contextFactory, DefaultNormalizer $normalizer)
    {
        $this->contextFactory = $contextFactory;
        $this->normalizer = $normalizer;
    }

    /**
     * Get data type
     */
    private function getDataType($data): string
    {
        if (\is_object($data)) {
            return \get_class($data);
        }
        return \gettype($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (null === $object) {
            return null;
        }

        return $this->normalizer->normalize(
            $this->getDataType($object),
            $object,
            $this->contextFactory->createContext($context, true)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (null === $data) {
            return null;
        }

        return $this->normalizer->denormalize(
            $class,
            $data,
            $this->contextFactory->createContext($context, true)
        );
    }
}
