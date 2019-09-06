<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Functional\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated5\MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Functional\MockArticle;

use MakinaCorpus\Normalizer as Helper;

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
        $value = Helper\find_value($input, ['id'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
        }
        \call_user_func(self::$accessor, $ret, 'id', $value);

        // Denormalize 'createdAt' property
        $value = Helper\find_value($input, ['createdAt'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('DateTimeInterface', $value, $context);
        }
        \call_user_func(self::$accessor, $ret, 'createdAt', $value);

        // Denormalize 'updatedAt' property
        $value = Helper\find_value($input, ['updatedAt'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('DateTimeInterface', $value, $context);
        }
        \call_user_func(self::$accessor, $ret, 'updatedAt', $value);

        // Denormalize 'authors' collection property
        $propValue = [];
        $values = Helper\find_value($input, ['authors'], $context);
        if (!\is_iterable($values)) {
            $values = Helper\to_string($value, $context);
            $propValue[] = $values;
        } else {
            foreach ($values as $index => $value) {
                try {
                    $context->enter((string)$index);
                    $value = Helper\to_string($value, $context);
                    $propValue[$index] = $value; 
                } finally {
                    $context->leave();
                }
            }
        }
        \call_user_func(self::$accessor, $ret, 'authors', $propValue);

        // Denormalize 'title' property
        $value = Helper\find_value($input, ['title'], $context);
        $value = Helper\to_string($value, $context);
        \call_user_func(self::$accessor, $ret, 'title', $value);

        // Denormalize 'text' property
        $value = Helper\find_value($input, ['text'], $context);
        if (null !== $value) {
            $value = \Generated5\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::denormalize($value, $context, $normalizer);
        }
        \call_user_func(self::$accessor, $ret, 'text', $value);

        return $ret;
    }
}

MockArticleNormalizer::$accessor = \Closure::bind(
    static function (MockArticle $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, MockArticle::class
);
