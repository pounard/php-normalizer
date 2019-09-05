<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockWithTitle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated2\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle;
use MakinaCorpus\Normalizer\Context;

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
        $value = self::find('title', $input, ['title'], $context);
        if (null === $value) {
            $context->addError("Property 'title' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!\gettype($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'title', $value);

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

MockWithTitleNormalizer::$accessor = \Closure::bind(
    static function (MockWithTitle $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockWithTitle::class
);
