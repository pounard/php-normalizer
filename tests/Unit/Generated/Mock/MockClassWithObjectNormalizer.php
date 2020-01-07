<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject;

final class MockClassWithObjectNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        $ret['object'] = (null === $object->object ? null : MockClassWithNullableIntNormalizer::normalize($object->object, $context, $normalizer));
    },
    null, MockClassWithObject::class
);

/**
 * Denormalizer for properties of MockClassWithObject.
 */
MockClassWithObjectNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithObject $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        if (!isset($input['object'])) {
            $context->nullValueError('MakinaCorpus\\Normalizer\\Tests\\Unit\\Mock\\MockClassWithNullableInt');
        } else {
            $instance->object = ($input['object'] instanceof MockClassWithNullableInt
                ? $input['object']
                : MockClassWithNullableIntNormalizer::denormalize($input['object'], $context, $denormalizer)
            );
        }
    },
    null, MockClassWithObject::class
);
