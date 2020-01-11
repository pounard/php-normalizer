<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt;

final class MockClassWithNullableIntNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt instance into an array.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::normalize()
     */
    public static function normalize($object, Context $context, ?callable $normalizer = null): array
    {
        $ret = [];
        (self::$normalizer0)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        $ret['nullableInt'] = (null === $object->nullableInt ? null : (int)$object->nullableInt);

    },
    null, MockClassWithNullableInt::class
);

/**
 * Denormalizer for properties of MockClassWithNullableInt.
 */
MockClassWithNullableIntNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithNullableInt $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        $instance->nullableInt = isset($input['nullableInt']) ? RuntimeHelper::toInt($input['nullableInt'], $context) : null;

    },
    null, MockClassWithNullableInt::class
);

