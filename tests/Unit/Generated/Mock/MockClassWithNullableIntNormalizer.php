<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt;

/**
 * Public implementation of (de)normalizer for class MockClassWithNullableInt.
 */
final class MockClassWithNullableIntNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithNullableInt
    {
        $ret = (new \ReflectionClass(MockClassWithNullableInt::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithNullableInt.
 */
MockClassWithNullableIntNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithNullableInt $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'nullableInt' property
        $ret['nullableInt'] = null === $object->nullableInt ? null : (int)$object->nullableInt;
    },
    null, MockClassWithNullableInt::class
);

/**
 * Denormalizer for properties of MockClassWithNullableInt.
 */
MockClassWithNullableIntNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithNullableInt $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'nullableInt' nullable property
        $option = Helper::find($input, ['nullableInt'], $context);
        if ($option->success) {
            if (null === $option->value) {
                $instance->nullableInt = null;
            } else {
                $instance->nullableInt = Helper::toInt($option->value);
            }
        }
    },
    null, MockClassWithNullableInt::class
);
