<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Normalizer;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\InvalidValueTypeError;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * UUID (de)normalizer.
 */
final class UuidNormalizer implements CustomNormalizer, CustomDenormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        if (!$object instanceof UuidInterface) {
            throw new InvalidValueTypeError(UuidInterface::class, Helper::getType($object));
        }

        return (string)$object;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        if (!\is_string($data)) {
            throw new InvalidValueTypeError('string', Helper::getType($data));
        }

        return Uuid::fromString($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return $type === UuidInterface::class || $type === Uuid::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return $type === UuidInterface::class || $type === Uuid::class;
    }
}
