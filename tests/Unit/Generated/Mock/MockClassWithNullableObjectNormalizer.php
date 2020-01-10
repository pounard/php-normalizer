<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject;

final class MockClassWithNullableObjectNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        try {
            $context->enter('nullableObject');
            $ret['nullableObject'] = (null === $object->nullableObject ? null : MockClassWithNullableIntNormalizer::normalize($object->nullableObject, $context, $normalizer));
        } finally {
            $context->leave();
        }

    },
    null, MockClassWithNullableObject::class
);

/**
 * Denormalizer for properties of MockClassWithNullableObject.
 */
MockClassWithNullableObjectNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithNullableObject $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('nullableObject');
            $instance->nullableObject = isset($input['nullableObject']) ? ($input['nullableObject'] instanceof MockClassWithNullableInt
                ? $input['nullableObject']
                : MockClassWithNullableIntNormalizer::denormalize($input['nullableObject'], $context, $denormalizer)
            ) : null;
        } finally {
            $context->leave();
        }

    },
    null, MockClassWithNullableObject::class
);

