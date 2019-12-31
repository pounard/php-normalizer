<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray;

/**
 * Public implementation of (de)normalizer for class MockClassWithObjectArray.
 */
final class MockClassWithObjectArrayNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithObjectArray
    {
        $ret = (new \ReflectionClass(MockClassWithObjectArray::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithObjectArray.
 */
MockClassWithObjectArrayNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithObjectArray $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'objectArray' property
        $ret['objectArray'] = [];
        if ($object->objectArray) {
            foreach ($object->objectArray as $index => $value) {
                if (null === $value) {
                    $ret['objectArray'][$index] = null;
                } else {
                    $ret['objectArray'][$index] = \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::normalize($value, $context, $normalizer);
                }
            }
        }
    },
    null, MockClassWithObjectArray::class
);

/**
 * Denormalizer for properties of MockClassWithObjectArray.
 */
MockClassWithObjectArrayNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithObjectArray $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'objectArray' collection property
        $option = Helper::find($input, ['objectArray'], $context);
        if ($option->success && $option->value) {
            if (!\is_iterable($option->value)) {
                $option->value = (array)$option->value;
            }
            if ($option->value) {
                $instance->objectArray = [];
                foreach ($option->value as $index => $value) {
                    if (null === $value) {
                        Helper::error("Property value in collection cannot be null");
                        $instance->objectArray[$index] = null;
                    } else {
                        $instance->objectArray[$index] = \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::denormalize($value, $context, $denormalizer);
                    }
                }
            }
        }
    },
    null, MockClassWithObjectArray::class
);
