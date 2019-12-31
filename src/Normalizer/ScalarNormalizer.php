<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Normalizer;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\InvalidValueTypeError;
use MakinaCorpus\Normalizer\UnsupportedTypeError;

/**
 * Scalar types normalizer and denormalizer
 */
final class ScalarNormalizer implements CustomNormalizer, CustomDenormalizer
{
    /**
     * Nice thing with scalar, (de)normalization goes both ways
     */
    private function handleValue(string $type, $object, Context $context)
    {
        if (null === $object) {
            return $object;
        }
        if (!\is_scalar($object) && 'null' !== $type) {
            throw new InvalidValueTypeError($type, Helper::getType($object));
        }

        switch ($type) {

            case 'bool':
                return (bool)$object;

            case 'float':
                return (float)$object;

            case 'int':
                return (int)$object;

            case 'null':
                // Null type can also mean that type could not be determined
                // allow value passthrough.
                return $object;

            case 'string':
                return (string)$object;

            default:
                throw new UnsupportedTypeError($type);
        }
    }

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

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return $type === 'bool' || $type === 'float' || $type === 'int' || $type === 'null' || $type === 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return $type === 'bool' || $type === 'float' || $type === 'int' || $type === 'null' || $type === 'string';
    }
}
