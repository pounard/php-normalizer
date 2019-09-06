<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated5\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat;
use MakinaCorpus\Normalizer\Context;

use MakinaCorpus\Normalizer as Helper;

final class MockTextWithFormatNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

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

        // Denormalize 'text' property
        $value = Helper\find_value($input, ['text', 'value'], $context);
        $value = Helper\to_string($value, $context);
        \call_user_func(self::$accessor, $ret, 'text', $value);

        // Denormalize 'format' property
        $value = Helper\find_value($input, ['format'], $context);
        $value = Helper\to_string($value, $context);
        \call_user_func(self::$accessor, $ret, 'format', $value);

        return $ret;
    }
}

MockTextWithFormatNormalizer::$accessor = \Closure::bind(
    static function (MockTextWithFormat $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockTextWithFormat::class
);
