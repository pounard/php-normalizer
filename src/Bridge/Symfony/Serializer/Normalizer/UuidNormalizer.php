<?php

namespace MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes and denormalizes everything that is a value type implementing
 * the IrpAuto\Common\StringType interface.
 */
final class UuidNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UuidInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($object instanceof UuidInterface) {
            return (string)$object;
        }

        // @codeCoverageIgnoreStart
        throw new InvalidArgumentException(\sprintf('The object must implement the "%s".', UuidInterface::class));
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return '[]' !== \substr($type, -2) && (UuidInterface::class === $type || (
            \class_exists($type) &&
            \in_array(UuidInterface::class, \class_implements($type))
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (null === $data) {
            return null;
        }
        return Uuid::fromString($data);
    }
}
