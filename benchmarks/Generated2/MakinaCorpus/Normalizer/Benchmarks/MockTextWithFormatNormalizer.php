<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated2\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat;
use MakinaCorpus\Normalizer\Context;

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
        $value = self::find('text', $input, ['text'], $context);
        if (null === $value) {
            $context->addError("Property 'text' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!\gettype($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'text', $value);

        // Denormalize 'format' property
        $value = self::find('format', $input, ['format'], $context);
        if (null === $value) {
            $context->addError("Property 'format' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!\gettype($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'format', $value);

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

MockTextWithFormatNormalizer::$accessor = \Closure::bind(
    static function (MockTextWithFormat $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockTextWithFormat::class
);
