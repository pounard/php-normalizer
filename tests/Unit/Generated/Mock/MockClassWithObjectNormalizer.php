<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject;

/**
 * Public implementation of (de)normalizer for class MockClassWithObject.
 */
final class MockClassWithObjectNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithObject
    {
        $ret = (new \ReflectionClass(MockClassWithObject::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithObject.
 */
MockClassWithObjectNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithObject $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'object' property
        $ret['object'] = null === $object->object ? null : \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::normalize($object->object, $context, $normalizer);
    },
    null, MockClassWithObject::class
);

/**
 * Denormalizer for properties of MockClassWithObject.
 */
MockClassWithObjectNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithObject $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'object' required property
        $option = Helper::find($input, ['object'], $context);
        if (!$option->success || null === $option->value) {
            Helper::error(\sprintf("'%s' cannot be null", 'object'), $context);
        } else {
            $instance->object = \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::denormalize($option->value, $context, $denormalizer);
        }
    },
    null, MockClassWithObject::class
);
