<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithString.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithString;

/**
 * Public implementation of (de)normalizer for class MockClassWithString.
 */
final class MockClassWithStringNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithString instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithString instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithString
    {
        $ret = (new \ReflectionClass(MockClassWithString::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithString.
 */
MockClassWithStringNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithString $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'string' property
        $ret['string'] = (null === $object->string ? null : (string)$object->string);
    },
    null, MockClassWithString::class
);

/**
 * Denormalizer for properties of MockClassWithString.
 */
MockClassWithStringNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithString $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'string' required property
        if (!isset($input['string'])) {
            $context->nullValueError('string');
        } else {
            $instance->string = \MakinaCorpus\Normalizer\Helper::toString($input['string'], $context);
        }
    },
    null, MockClassWithString::class
);
