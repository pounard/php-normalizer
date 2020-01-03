<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated8\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat;
use MakinaCorpus\Normalizer\Context;

/**
 * Public implementation of (de)normalizer for class MockTextWithFormat.
 */
final class MockTextWithFormatNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockTextWithFormat
    {
        $ret = (new \ReflectionClass(MockTextWithFormat::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockTextWithFormat.
 */
MockTextWithFormatNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockTextWithFormat $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'text' property
        $ret['text'] = (null === $object->text ? null : (string)$object->text);

        // Normalize 'format' property
        $ret['format'] = (null === $object->format ? null : (string)$object->format);
    },
    null, MockTextWithFormat::class
);

/**
 * Denormalizer for properties of MockTextWithFormat.
 */
MockTextWithFormatNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockTextWithFormat $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'text' nullable property
        $instance->text = isset($input['text']) ? \MakinaCorpus\Normalizer\Helper::toString($input['text'], $context) : null;

        // Denormalize 'format' required property
        if (!isset($input['format'])) {
            $context->nullValueError('string');
        } else {
            $instance->format = \MakinaCorpus\Normalizer\Helper::toString($input['format'], $context);
        }
    },
    null, MockTextWithFormat::class
);
