<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject;

/**
 * Public implementation of (de)normalizer for class MockClassWithNullableObject.
 */
final class MockClassWithNullableObjectNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithNullableObject
    {
        $ret = (new \ReflectionClass(MockClassWithNullableObject::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithNullableObject.
 */
MockClassWithNullableObjectNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithNullableObject $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'nullableObject' property
        $ret['nullableObject'] = null === $object->nullableObject ? null : \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::normalize($object->nullableObject, $context, $normalizer);
    },
    null, MockClassWithNullableObject::class
);

/**
 * Denormalizer for properties of MockClassWithNullableObject.
 */
MockClassWithNullableObjectNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithNullableObject $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'nullableObject' nullable property
        $instance->nullableObject = isset($input['nullableObject']) ? \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::denormalize($input['nullableObject'], $context, $denormalizer) : null;
    },
    null, MockClassWithNullableObject::class
);
