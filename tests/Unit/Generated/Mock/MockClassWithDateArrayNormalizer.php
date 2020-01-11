<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use DateTime;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray;

final class MockClassWithDateArrayNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithDateArray
    {
        $ret = (new \ReflectionClass(MockClassWithDateArray::class))->newInstanceWithoutConstructor();
        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithDateArray.
 */
MockClassWithDateArrayNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithDateArray $object, Context $context, ?callable $normalizer = null): void {
        $ret['dateArray'] = [];
        if ($object->dateArray) {
            foreach ($object->dateArray as $index => $value) {
                if (null === $value) {
                    $ret['dateArray'][$index] = null;
                } else {
                    $ret['dateArray'][$index] = $value->format('Y-m-d\\TH:i:sP');
                }
            }
        }

    },
    null, MockClassWithDateArray::class
);

/**
 * Denormalizer for properties of MockClassWithDateArray.
 */
MockClassWithDateArrayNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithDateArray $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        if (isset($input['dateArray'])) {
            if (!\is_iterable($input['dateArray'])) {
                $input['dateArray'] = (array)$input['dateArray'];
            }
            if ($input['dateArray']) {
                $instance->dateArray = [];
                foreach ($input['dateArray'] as $index => $value) {
                    if (null === $value) {
                        $context->nullValueError('DateTime');
                        $instance->dateArray[$index] = null;
                    } else {
                        $instance->dateArray[$index] = ($value instanceof DateTime
                            ? $value
                            : RuntimeHelper::toDate($value, $context)
                        );
                    }
                }
            }
        }

    },
    null, MockClassWithDateArray::class
);

