<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray;

final class MockClassWithObjectArrayNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        if (isset($input['objectArray'])) {
            if (!\is_iterable($input['objectArray'])) {
                $input['objectArray'] = (array)$input['objectArray'];
            }
            if ($input['objectArray']) {
                $instance->objectArray = [];
                foreach ($input['objectArray'] as $index => $value) {
                    if (null === $value) {
                        $context->nullValueError('MakinaCorpus\\Normalizer\\Tests\\Unit\\Mock\\MockClassWithNullableInt');
                        $instance->objectArray[$index] = null;
                    } else {
                        $instance->objectArray[$index] = ($value instanceof \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt
                            ? $value
                            : \MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock\MockClassWithNullableIntNormalizer::denormalize($value, $context, $denormalizer)
                        );
                    }
                }
            }
        }
    },
    null, MockClassWithObjectArray::class
);
