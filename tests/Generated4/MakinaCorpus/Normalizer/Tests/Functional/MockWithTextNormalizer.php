<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Functional\MockWithText.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated4\MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;

use MakinaCorpus\Normalizer as Helper;

final class MockWithTextNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Functional\MockWithText instances.
     *
     * @param callable $normalizer
     *   A callback that will hydrate externally handled values, parameters are:
     *      - string $type PHP native type to hydrate
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $normalizer = null): MockWithText
    {
        $ret = (new \ReflectionClass(MockWithText::class))->newInstanceWithoutConstructor();

        // Denormalize 'title' property
        $value = Helper\find_value($input, ['title'], $context);
        $value = Helper\to_string($value, $context);
        \call_user_func(self::$accessor, $ret, 'title', $value);

        // Denormalize 'text' property
        $value = Helper\find_value($input, ['text', 'markup'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('MakinaCorpus\\Normalizer\\Benchmarks\\MockTextWithFormat', $value, $context);
            if (!(null === $value || $value instanceof \MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        \call_user_func(self::$accessor, $ret, 'text', $value);

        return $ret;
    }
}

MockWithTextNormalizer::$accessor = \Closure::bind(
    static function (MockWithText $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockWithText::class
);
