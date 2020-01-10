<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray;

final class MockClassWithIntArrayNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        try {
            $context->enter('intArray');
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
        } finally {
            $context->leave();
        }

    },
    null, MockClassWithIntArray::class
);

/**
 * Denormalizer for properties of MockClassWithIntArray.
 */
MockClassWithIntArrayNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithIntArray $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('intArray');
            if (isset($input['intArray'])) {
                if (!\is_iterable($input['intArray'])) {
                    $input['intArray'] = (array)$input['intArray'];
                }
                if ($input['intArray']) {
                    $instance->intArray = [];
                    foreach ($input['intArray'] as $index => $value) {
                        if (null === $value) {
                            $context->nullValueError('int');
                            $instance->intArray[$index] = null;
                        } else {
                            $instance->intArray[$index] = RuntimeHelper::toInt($value, $context);
                        }
                    }
                }
            }
        } finally {
            $context->leave();
        }

    },
    null, MockClassWithIntArray::class
);

