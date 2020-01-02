<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Normalizer;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\InvalidValueTypeError;
use MakinaCorpus\Normalizer\Option;
use MakinaCorpus\Normalizer\UnsupportedTypeError;

/**
 * \DateTime and \DateTimeImmutable (de)normalizer.
 */
final class DateTimeNormalizer implements CustomNormalizer, CustomDenormalizer
{
    /**
     * Create date using format
     */
    private static function createImmutableDateWithFormat(string $format, string $data): \DateTimeInterface
    {
        try {
            if ($date = \DateTimeImmutable::createFromFormat($format, $data)) {
                return $date;
            }
            if ($date = new \DateTimeImmutable($data)) {
                return $date;
            }
        } catch (\Throwable $e) {
            throw new InvalidValueTypeError(\sprintf(
                "Could not parse date '%s' with format '%s'",
                $data, $format
            ), null, $e->getCode(), $e);
        }
    }

    /**
     * Create date using format
     */
    private static function createDateWithFormat(string $format, string $data): \DateTimeInterface
    {
        try {
            if ($date = \DateTime::createFromFormat($format, $data)) {
                return $date;
            }
            if ($date = new \DateTime($data)) {
                return $date;
            }
        } catch (\Throwable $e) {
            throw new InvalidValueTypeError(\sprintf(
                "Could not parse date '%s' with format '%s'",
                $data, $format
            ), null, $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        switch ($type) {
            case \DateTimeImmutable::class:
            case \DateTimeInterface::class:
            case \DateTime::class:
                if (!$object instanceof \DateTimeInterface) {
                    throw new InvalidValueTypeError($type, Helper::getType($object));
                }
                return $object->format(
                    $context->getOption(Option::DATE_FORMAT, \DateTime::ISO8601)
                );
        }

        throw new UnsupportedTypeError($type);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        if (!\is_string($data)) {
            throw new InvalidValueTypeError(\sprintf(
                "Invalid data type provided, awaiting for 'string', got '%s'",
                Helper::getType($data)
            ));
        }

        $format = $context->getOption(Option::DATE_FORMAT, \DateTime::ISO8601);

        switch ($type) {

            case \DateTimeImmutable::class:
            case \DateTimeInterface::class:
                return self::createImmutableDateWithFormat($format, $data);

            case \DateTime::class:
                return self::createDateWithFormat($format, $data);
        }

        throw new UnsupportedTypeError($type);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return $type === \DateTime::class || $type === \DateTimeInterface::class || $type === \DateTimeImmutable::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return $type === \DateTime::class || $type === \DateTimeInterface::class || $type === \DateTimeImmutable::class;
    }
}
