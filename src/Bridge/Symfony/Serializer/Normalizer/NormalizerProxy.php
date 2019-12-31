<?php

namespace MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer;

use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Normalizer;
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
    public function __construct(ContextFactory $contextFactory, Normalizer $normalizer)
    {
        $this->contextFactory = $contextFactory;
        $this->normalizer = $normalizer;
    }

    /**
     * Get data type
     */
    private function getDataType($data): string
    {
        return Helper::getType($data);
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
