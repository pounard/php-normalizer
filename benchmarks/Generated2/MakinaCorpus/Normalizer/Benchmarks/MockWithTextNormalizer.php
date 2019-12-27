<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockWithText.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated2\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockWithText;
use MakinaCorpus\Normalizer\Context;

final class MockWithTextNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\MockWithText instances.
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
        $value = self::find('title', $input, ['title'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!null === $value || \MakinaCorpus\Normalizer\Helper::getType($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'title', $value);

        // Denormalize 'text' property
        $value = self::find('text', $input, ['text', 'markup'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('MakinaCorpus\\Normalizer\\Benchmarks\\MockTextWithFormat', $value, $context);
        }
        if (!null === $value || $value instanceof \MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat) {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'text', $value);

        return $ret;
    }

    /**
     * Find value matching in array
     */
    private static function find(string $propName, array $input, array $names, Context $context)
    {
        $found = $value = null;
        foreach ($names as $name) {
            if (\array_key_exists($name, $input)) {
                if ($found) {
                    $context->addError(\sprintf("Property '%s' found in '%s' but was already found in '%s'", $propName, $found, $name));
                } else {
                    $found = $name;
                    $value = $input[$name];
                }
            }
        }
        return $value;
    }
}

MockWithTextNormalizer::$accessor = \Closure::bind(
    static function (MockWithText $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockWithText::class
);
