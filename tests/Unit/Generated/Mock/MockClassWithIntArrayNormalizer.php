<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray;

/**
 * Public implementation of (de)normalizer for class MockClassWithIntArray.
 */
final class MockClassWithIntArrayNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithIntArray
    {
        $ret = (new \ReflectionClass(MockClassWithIntArray::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithIntArray.
 */
MockClassWithIntArrayNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithIntArray $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'intArray' property
        $ret['intArray'] = [];
        if ($object->intArray) {
            foreach ($object->intArray as $index => $value) {
                if (null === $value) {
                    $ret['intArray'][$index] = null;
                } else {
                    $ret['intArray'][$index] = (int)$value;
                }
            }
        }
    },
    null, MockClassWithIntArray::class
);

/**
 * Denormalizer for properties of MockClassWithIntArray.
 */
MockClassWithIntArrayNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithIntArray $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'intArray' collection property
        $option = Helper::find($input, ['intArray'], $context);
        if ($option->success && $option->value) {
            if (!\is_iterable($option->value)) {
                $option->value = (array)$option->value;
            }
            if ($option->value) {
                $instance->intArray = [];
                foreach ($option->value as $index => $value) {
                    if (null === $value) {
                        Helper::error("Property value in collection cannot be null");
                        $instance->intArray[$index] = null;
                    } else {
                        $instance->intArray[$index] = Helper::toInt($value);
                    }
                }
            }
        }
    },
    null, MockClassWithIntArray::class
);
