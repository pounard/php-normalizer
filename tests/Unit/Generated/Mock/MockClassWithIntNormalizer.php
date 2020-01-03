<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithInt.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithInt;

final class MockClassWithIntNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithInt instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithInt instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithInt
    {
        $ret = (new \ReflectionClass(MockClassWithInt::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithInt.
 */
MockClassWithIntNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithInt $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'int' property
        $ret['int'] = (null === $object->int ? null : (int)$object->int);
    },
    null, MockClassWithInt::class
);

/**
 * Denormalizer for properties of MockClassWithInt.
 */
MockClassWithIntNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithInt $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'int' required property
        if (!isset($input['int'])) {
            $context->nullValueError('int');
        } else {
            $instance->int = \MakinaCorpus\Normalizer\Helper::toInt($input['int'], $context);
        }
    },
    null, MockClassWithInt::class
);
