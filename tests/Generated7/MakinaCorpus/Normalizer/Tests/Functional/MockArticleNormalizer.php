<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Functional\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated7\MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer as Helper;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Functional\MockArticle;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;

/**
 * Public implementation of (de)normalizer for class MockArticle.
 */
final class MockArticleNormalizer
{
    /** @var callable */
    public static $denormalizer0;

    /** @var callable */
    public static $denormalizer1;

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

        (self::$denormalizer0)($ret, $input, $context, $normalizer);
        (self::$denormalizer1)($ret, $input, $context, $normalizer);

        return $ret;
    }
}

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $normalizer = null): void {

        // Denormalize 'id' property
        $value = Helper\find_value($input, ['id'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'id' cannot be null", $context);
            } else if (!($value instanceof \Ramsey\Uuid\UuidInterface)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->id = $value;

        // Denormalize 'createdAt' property
        $value = Helper\find_value($input, ['createdAt'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('DateTimeInterface', $value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'createdAt' cannot be null", $context);
            } else if (!($value instanceof \DateTimeInterface)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->createdAt = $value;

        // Denormalize 'updatedAt' property
        $value = Helper\find_value($input, ['updatedAt'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('DateTimeInterface', $value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'updatedAt' cannot be null", $context);
            } else if (!($value instanceof \DateTimeInterface)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->updatedAt = $value;

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
        $instance->authors = $propValue;
    },
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $normalizer = null): void {

        // Denormalize 'title' property
        $value = Helper\find_value($input, ['title'], $context);
        $value = Helper\to_string($value, $context);
        $instance->title = $value;

        // Denormalize 'text' property
        $value = Helper\find_value($input, ['text'], $context);
        if (null !== $value) {
            $value = \Generated7\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::denormalize($value, $context, $normalizer);
            if (!(null === $value || $value instanceof \MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->text = $value;
    },
    null, MockWithText::class
);
