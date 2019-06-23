<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Scalar types normalizer and denormalizer
 */
final class ScalarNormalizer implements DeclarativeNormalizer, DeclarativeDenormalizer
{
    use BothDeclarativeNormalizer;

    /**
     * {@inheritdoc}
     */
    protected function getHandledTypes(): array
    {
        return ['bool', 'float', 'int', 'null', 'string'];
    }

    /**
     * Nice thing with scalar, (de)normalization goes both ways
     */
    private function handleValue(string $type, $object, Context $context)
    {
        if (null === $object) {
            return $object;
        }

        if (!\is_scalar($object)) {
            throw new InvalidValueTypeError(\sprintf(
                "Invalid data type provided, awaiting for '%s', got '%s'",
                $type, \gettype($object)
            ));
        }

        switch ($type) {

            case 'bool':
                return (bool)$object;

            case 'float':
                return (float)$object;

            case 'int':
                return (int)$object;

            case 'null':
                return null;

            case 'string':
                return (string)$object;

            default:
                throw new UnupportedTypeError(\sprintf("Unsupported type '%s'", $type));
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
}

/**
 * \DateTime and \DateTimeImmutable normalizer and denormalizer
 */
final class DateNormalizer implements Normalizer, Denormalizer
{
    use BothDeclarativeNormalizer;

    /**
     * {@inheritdoc}
     */
    protected function getHandledTypes(): array
    {
        return [\DateTime::class, \DateTimeInterface::class, \DateTimeImmutable::class];
    }

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
            ));
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
            ), null, $e);
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
                    throw new InvalidValueTypeError(\sprintf(
                        "Invalid data type provided, awaiting for '%s', got '%s'",
                        $type, \gettype($object)
                    ));
                }
                return $object->format(
                    $context->getOption(Option::DATE_FORMAT, \DateTime::ISO8601)
                );
        }

        throw new UnupportedTypeError(\sprintf("Unsupported type '%s'", $type));
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        if (!\is_string($data)) {
            throw new InvalidValueTypeError(\sprintf(
                "Invalid data type provided, awaiting for 'string', got '%s'",
                \gettype($data)
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

        throw new UnupportedTypeError(\sprintf("Unsupported type '%s'", $type));
    }
}

/**
 * Scalar types normalizer and denormalizer
 */
final class UuidNormalizer implements DeclarativeNormalizer, DeclarativeDenormalizer
{
    use BothDeclarativeNormalizer;

    /**
     * {@inheritdoc}
     */
    protected function getHandledTypes(): array
    {
        return [UuidInterface::class, Uuid::class];
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        if (!$object instanceof UuidInterface) {
            throw new InvalidValueTypeError(\sprintf(
                "Invalid data type provided, awaiting for '%s', got '%s'",
                UuidInterface::class, \gettype($object)
            ));
        }

        return (string)$object;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        if (!\is_string($data)) {
            throw new InvalidValueTypeError(\sprintf(
                "Invalid data type provided, awaiting for 'string', got '%s'",
                \gettype($data)
            ));
        }

        return Uuid::fromString($data);
    }
}
