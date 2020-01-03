<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithFloat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithFloat;

final class MockClassWithFloatNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithFloat instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithFloat instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithFloat
    {
        $ret = (new \ReflectionClass(MockClassWithFloat::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithFloat.
 */
MockClassWithFloatNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithFloat $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'float' property
        $ret['float'] = (null === $object->float ? null : (float)$object->float);
    },
    null, MockClassWithFloat::class
);

/**
 * Denormalizer for properties of MockClassWithFloat.
 */
MockClassWithFloatNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithFloat $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'float' required property
        if (!isset($input['float'])) {
            $context->nullValueError('float');
        } else {
            $instance->float = \MakinaCorpus\Normalizer\Helper::toFloat($input['float'], $context);
        }
    },
    null, MockClassWithFloat::class
);
