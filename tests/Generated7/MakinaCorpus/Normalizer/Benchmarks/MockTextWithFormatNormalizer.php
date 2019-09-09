<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated7\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer as Helper;
use MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat;
use MakinaCorpus\Normalizer\Context;

/**
 * Public implementation of (de)normalizer for class MockTextWithFormat.
 */
final class MockTextWithFormatNormalizer
{
    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat instances.
     *
     * @param callable $normalizer
     *   A callback that will hydrate externally handled values, parameters are:
     *      - string $type PHP native type to hydrate
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $normalizer = null): MockTextWithFormat
    {
        $ret = (new \ReflectionClass(MockTextWithFormat::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $normalizer);

        return $ret;
    }
}

/**
 * Denormalizer for properties of MockTextWithFormat.
 */
MockTextWithFormatNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockTextWithFormat $instance, array $input, Context $context, ?callable $normalizer = null): void {

        // Denormalize 'text' property
        $value = Helper\find_value($input, ['text', 'value'], $context);
        $value = Helper\to_string($value, $context);
        $instance->text = $value;

        // Denormalize 'format' property
        $value = Helper\find_value($input, ['format'], $context);
        $value = Helper\to_string($value, $context);
        $instance->format = $value;
    },
    null, MockTextWithFormat::class
);
