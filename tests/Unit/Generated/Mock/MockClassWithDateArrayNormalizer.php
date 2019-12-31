<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray;

/**
 * Public implementation of (de)normalizer for class MockClassWithDateArray.
 */
final class MockClassWithDateArrayNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray instances.
     *
     * @param callable $normalizer
     *   A callback that will normalize externally handled values, parameters are:
     *      - mixed $input raw value from denormalized data
     *      - Context $context the context
     */
    public static function normalize($object, Context $context, ?callable $normalizer = null): array
    {
        $ret = [];

        (self::$normalizer0)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithDateArray
    {
        $ret = (new \ReflectionClass(MockClassWithDateArray::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithDateArray.
 */
MockClassWithDateArrayNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithDateArray $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'dateArray' property
        $ret['dateArray'] = [];
        if ($object->dateArray) {
            foreach ($object->dateArray as $index => $value) {
                if (null === $value) {
                    $ret['dateArray'][$index] = null;
                } else {
                    $ret['dateArray'][$index] = $normalizer ? $normalizer($value, $context, $normalizer) : $value;
                }
            }
        }
    },
    null, MockClassWithDateArray::class
);

/**
 * Denormalizer for properties of MockClassWithDateArray.
 */
MockClassWithDateArrayNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithDateArray $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'dateArray' collection property
        $option = Helper::find($input, ['dateArray'], $context);
        if ($option->success && $option->value) {
            if (!\is_iterable($option->value)) {
                $option->value = (array)$option->value;
            }
            if ($option->value) {
                $instance->dateArray = [];
                foreach ($option->value as $index => $value) {
                    if (null === $value) {
                        Helper::error("Property value in collection cannot be null");
                        $instance->dateArray[$index] = null;
                    } else {
                        $instance->dateArray[$index] = $denormalizer ? $denormalizer(\DateTime::class, $value, $context, $denormalizer) : $value;
                    }
                }
            }
        }
    },
    null, MockClassWithDateArray::class
);
