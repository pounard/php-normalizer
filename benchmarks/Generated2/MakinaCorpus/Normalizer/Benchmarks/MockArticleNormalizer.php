<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated2\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockArticle;
use MakinaCorpus\Normalizer\Context;

final class MockArticleNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\MockArticle instances.
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

        // Denormalize 'title' property
        $value = self::find('title', $input, ['title'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!null === $value || \gettype($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'title', $value);

        // Denormalize 'text' property
        $value = self::find('text', $input, ['text'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('null', $value, $context);
        }
        if (!null === $value || \gettype($value) === 'null') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'text', $value);

        // Denormalize 'id' property
        $value = self::find('id', $input, ['id'], $context);
        if (null === $value) {
            $context->addError("Property 'id' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!\gettype($value) === 'string') {
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
                if (\gettype($value) === 'string') {
                    $propValue[$index] = $value;
                } else {
                    $propValue[$index] = null;
                }
            }
        }
        \call_user_func(self::$accessor, $ret, 'authors', $propValue);

        // Denormalize 'foo' property
        $value = self::find('foo', $input, ['foo'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('null', $value, $context);
        }
        if (!null === $value || \gettype($value) === 'null') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'foo', $value);

        // Denormalize 'bar' property
        $value = self::find('bar', $input, ['bar'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('null', $value, $context);
        }
        if (!null === $value || \gettype($value) === 'null') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'bar', $value);

        // Denormalize 'baz' property
        $value = self::find('baz', $input, ['baz'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('null', $value, $context);
        }
        if (!null === $value || \gettype($value) === 'null') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'baz', $value);

        // Denormalize 'filename' property
        $value = self::find('filename', $input, ['filename'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('string', $value, $context);
        }
        if (!null === $value || \gettype($value) === 'string') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'filename', $value);

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
