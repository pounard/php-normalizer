<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockWithTitle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated4\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle;
use MakinaCorpus\Normalizer\Context;

use MakinaCorpus\Normalizer as Helper;

final class MockWithTitleNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\MockWithTitle instances.
     *
     * @param callable $normalizer
     *   A callback that will hydrate externally handled values, parameters are:
     *      - string $type PHP native type to hydrate
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $normalizer = null): MockWithTitle
    {
        $ret = (new \ReflectionClass(MockWithTitle::class))->newInstanceWithoutConstructor();

        // Denormalize 'title' property
        $value = Helper\find_value($input, ['title'], $context);
        $value = Helper\to_string($value, $context);
        \call_user_func(self::$accessor, $ret, 'title', $value);

        return $ret;
    }
}

MockWithTitleNormalizer::$accessor = \Closure::bind(
    static function (MockWithTitle $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockWithTitle::class
);
