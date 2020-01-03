<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated7\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\MockWithText;
use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Generator\Iterations as Helper;

/**
 * Public implementation of (de)normalizer for class MockArticle.
 */
final class MockArticleNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /** @var callable */
    public static $normalizer1;

    /** @var callable */
    public static $denormalizer1;

    /** @var callable */
    public static $normalizer2;

    /** @var callable */
    public static $denormalizer2;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\MockArticle instances.
     *
     * @param callable $normalizer
     *   A callback that will normalize externally handled values, parameters are:
     *      - mixed $input raw value from denormalized data
     *      - Context $context the context
     */
    public static function normalize($object, Context $context, ?callable $normalizer = null): array
    {
        $ret = [];

        (self::$normalizer0)($ret, $object, $context, $normalizer);
        (self::$normalizer1)($ret, $object, $context, $normalizer);
        (self::$normalizer2)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize MakinaCorpus\Normalizer\Benchmarks\MockArticle instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockArticle
    {
        $ret = (new \ReflectionClass(MockArticle::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);
        (self::$denormalizer1)($ret, $input, $context, $denormalizer);
        (self::$denormalizer2)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockWithTitle $object, Context $context, ?callable $normalizer = null): void {
        // Denormalize 'title' property
        $value = $object->title;
        $value = Helper\to_string($value, $context);
        $ret['title'] = $value;
    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'title' property
        $value = Helper\find_value($input, ['title'], $context);
        $value = Helper\to_string($value, $context);
        $instance->title = $value;
    },
    null, MockWithTitle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {
        // Denormalize 'text' property
        $value = $object->text;
        if (null !== $value) {
            $value = \Generated7\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::normalize($value, $context, $normalizer);
        }
        $ret['text'] = $value;
    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'text' property
        $value = Helper\find_value($input, ['text'], $context);
        if (null !== $value) {
            $value = \Generated7\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::denormalize($value, $context, $denormalizer);
            if (!(null === $value || $value instanceof \MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->text = $value;
    },
    null, MockWithText::class
);

/**
 * Normalizer for properties of MockArticle.
 */
MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, MockArticle $object, Context $context, ?callable $normalizer = null): void {

        // Denormalize 'id' property
        $value = $object->id;
        if (null !== $value && $normalizer) {
            $value = $normalizer($value, $context);
        }
        $ret['id'] = $value;

        // Denormalize 'createdAt' property
        $value = $object->createdAt;
        if (null !== $value && $normalizer) {
            $value = $normalizer($value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'createdAt' cannot be null", $context);
            }
        }
        $ret['createdAt'] = $value;

        // Denormalize 'updatedAt' property
        $value = $object->updatedAt;
        if (null !== $value && $normalizer) {
            $value = $normalizer($value, $context);
        }
        $ret['updatedAt'] = $value;

        // Denormalize 'authors' collection property
        $normalizedValues = [];
        $values = $object->authors;
        if (!\is_iterable($values)) {
            $values = Helper\to_string($value, $context);
            $normalizedValues[] = $values;
        } else {
            foreach ($values as $index => $value) {
                $value = Helper\to_string($value, $context);
                $normalizedValues[$index] = $value;
            }
        }
        $ret['authors'] = $normalizedValues;

        // Denormalize 'foo' property
        $value = $object->foo;
        $value = Helper\to_string($value, $context);
        $ret['foo'] = $value;

        // Denormalize 'bar' property
        $value = $object->bar;
        $value = Helper\to_int($value, $context);
        $ret['bar'] = $value;

        // Denormalize 'baz' property
        $value = $object->baz;
        $value = Helper\to_float($value, $context);
        $ret['baz'] = $value;

        // Denormalize 'filename' property
        $value = $object->filename;
        $value = Helper\to_string($value, $context);
        $ret['filename'] = $value;
    },
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'id' property
        $value = Helper\find_value($input, ['id'], $context);
        if (null !== $value && $denormalizer) {
            $value = $denormalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
            if (!(null === $value || $value instanceof \Ramsey\Uuid\UuidInterface)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->id = $value;

        // Denormalize 'createdAt' property
        $value = Helper\find_value($input, ['createdAt'], $context);
        if (null !== $value && $denormalizer) {
            $value = $denormalizer('DateTimeImmutable', $value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'createdAt' cannot be null", $context);
            } else if (!($value instanceof \DateTimeImmutable)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->createdAt = $value;

        // Denormalize 'updatedAt' property
        $value = Helper\find_value($input, ['updatedAt'], $context);
        if (null !== $value && $denormalizer) {
            $value = $denormalizer('DateTimeImmutable', $value, $context);
            if (!(null === $value || $value instanceof \DateTimeImmutable)) {
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
                $value = Helper\to_string($value, $context);
                $propValue[$index] = $value;
            }
        }
        $instance->authors = $propValue;

        // Denormalize 'foo' property
        $value = Helper\find_value($input, ['foo'], $context);
        $value = Helper\to_string($value, $context);
        $instance->foo = $value;

        // Denormalize 'bar' property
        $value = Helper\find_value($input, ['bar'], $context);
        $value = Helper\to_int($value, $context);
        $instance->bar = $value;

        // Denormalize 'baz' property
        $value = Helper\find_value($input, ['baz'], $context);
        $value = Helper\to_float($value, $context);
        $instance->baz = $value;

        // Denormalize 'filename' property
        $value = Helper\find_value($input, ['filename'], $context);
        $value = Helper\to_string($value, $context);
        $instance->filename = $value;
    },
    null, MockArticle::class
);
