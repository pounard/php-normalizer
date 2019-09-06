<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Functional\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated2\MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Functional\MockArticle;

final class MockArticleNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Functional\MockArticle instances.
     *
     * @param callable $normalizer
     *   A callback that will hydrate externally handled values, parameters are:
     *      - string $type PHP native type to hydrate
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $normalizer = null): MockArticle
    {
        $ret = (new \ReflectionClass(MockArticle::class))->newInstanceWithoutConstructor();

        // Denormalize 'id' property
        $value = self::find('id', $input, ['id'], $context);
        if (null === $value) {
            $context->addError("Property 'id' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
        }
        if (!$value instanceof \Ramsey\Uuid\UuidInterface) {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'id', $value);

        // Denormalize 'createdAt' property
        $value = self::find('createdAt', $input, ['createdAt'], $context);
        if (null === $value) {
            $context->addError("Property 'createdAt' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('DateTimeInterface', $value, $context);
        }
        if (!$value instanceof \DateTimeInterface) {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'createdAt', $value);

        // Denormalize 'updatedAt' property
        $value = self::find('updatedAt', $input, ['updatedAt'], $context);
        if (null === $value) {
            $context->addError("Property 'updatedAt' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('DateTimeInterface', $value, $context);
        }
        if (!$value instanceof \DateTimeInterface) {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'updatedAt', $value);

        // Denormalize 'authors' collection property
        $values = self::find('authors', $input, ['authors'], $context);
        if (null === $values) {
            $propValue = [];
        } else {
            if (!\is_iterable($values)) {
                $values = [$values];
            }
            $propValue = [];
            foreach ($values as $index => $value) {
                if (null !== $value && $normalizer) {
                    $value = $normalizer('string', $value, $context);
                }
                if (\MakinaCorpus\Normalizer\gettype_real($value) === 'string') {
                    $propValue[$index] = $value;
                } else {
                    $propValue[$index] = null;
                }
            }
        }
        \call_user_func(self::$accessor, $ret, 'authors', $propValue);

        // Denormalize 'title' property
        $value = self::find('title', $input, ['title'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!null === $value || \MakinaCorpus\Normalizer\gettype_real($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'title', $value);

        // Denormalize 'text' property
        $value = self::find('text', $input, ['text'], $context);
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

MockArticleNormalizer::$accessor = \Closure::bind(
    static function (MockArticle $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockArticle::class
);
