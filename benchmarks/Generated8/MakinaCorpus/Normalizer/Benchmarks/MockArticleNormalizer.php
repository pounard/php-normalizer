<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated8\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\MockWithText;
use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;

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
 * Normalizer for properties of MockArticle.
 */
MockArticleNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockArticle $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'id' property
        $ret['id'] = null === $object->id ? null : $normalizer ? $normalizer($object->id, $context, $normalizer) : $object->id;

        // Normalize 'foo' property
        $ret['foo'] = null === $object->foo ? null : (string)$object->foo;

        // Normalize 'bar' property
        $ret['bar'] = null === $object->bar ? null : (int)$object->bar;

        // Normalize 'baz' property
        $ret['baz'] = null === $object->baz ? null : (float)$object->baz;

        // Normalize 'filename' property
        $ret['filename'] = null === $object->filename ? null : (string)$object->filename;

        // Normalize 'createdAt' property
        $ret['createdAt'] = null === $object->createdAt ? null : $normalizer ? $normalizer($object->createdAt, $context, $normalizer) : $object->createdAt;

        // Normalize 'updatedAt' property
        $ret['updatedAt'] = null === $object->updatedAt ? null : $normalizer ? $normalizer($object->updatedAt, $context, $normalizer) : $object->updatedAt;

        // Normalize 'authors' property
        $ret['authors'] = [];
        if ($object->authors) {
            foreach ($object->authors as $index => $value) {
                $ret['authors'][$index] = (string)$value;
            }
        }
    },
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'id' required property
        $option = Helper::find($input, ['id'], $context);
        if ($option->success) {
            if (null === $option->value) {
                Helper::error(\sprintf("'%s' cannot be null", 'id'), $context);
            } else if (null === $option->value) {
                $instance->id = null;
            } else {
                $instance->id = $denormalizer ? $denormalizer(\Ramsey\Uuid\UuidInterface::class, $option->value, $context, $denormalizer) : $option->value;
            }
        }

        // Denormalize 'foo' nullable property
        $option = Helper::find($input, ['foo'], $context);
        if ($option->success) {
            if (null === $option->value) {
                $instance->foo = null;
            } else {
                $instance->foo = Helper::toString($option->value);
            }
        }

        // Denormalize 'bar' required property
        $option = Helper::find($input, ['bar'], $context);
        if ($option->success) {
            if (null === $option->value) {
                Helper::error(\sprintf("'%s' cannot be null", 'bar'), $context);
            } else if (null === $option->value) {
                $instance->bar = null;
            } else {
                $instance->bar = Helper::toInt($option->value);
            }
        }

        // Denormalize 'baz' required property
        $option = Helper::find($input, ['baz'], $context);
        if ($option->success) {
            if (null === $option->value) {
                Helper::error(\sprintf("'%s' cannot be null", 'baz'), $context);
            } else if (null === $option->value) {
                $instance->baz = null;
            } else {
                $instance->baz = Helper::toFloat($option->value);
            }
        }

        // Denormalize 'filename' required property
        $option = Helper::find($input, ['filename'], $context);
        if ($option->success) {
            if (null === $option->value) {
                Helper::error(\sprintf("'%s' cannot be null", 'filename'), $context);
            } else if (null === $option->value) {
                $instance->filename = null;
            } else {
                $instance->filename = Helper::toString($option->value);
            }
        }

        // Denormalize 'createdAt' required property
        $option = Helper::find($input, ['createdAt'], $context);
        if ($option->success) {
            if (null === $option->value) {
                Helper::error(\sprintf("'%s' cannot be null", 'createdAt'), $context);
            } else if (null === $option->value) {
                $instance->createdAt = null;
            } else {
                $instance->createdAt = $denormalizer ? $denormalizer(\DateTimeInterface::class, $option->value, $context, $denormalizer) : $option->value;
            }
        }

        // Denormalize 'updatedAt' nullable property
        $option = Helper::find($input, ['updatedAt'], $context);
        if ($option->success) {
            if (null === $option->value) {
                $instance->updatedAt = null;
            } else {
                $instance->updatedAt = $denormalizer ? $denormalizer(\DateTimeInterface::class, $option->value, $context, $denormalizer) : $option->value;
            }
        }

        // Denormalize 'authors' collection property
        $option = Helper::find($input, ['authors'], $context);
        if ($option->success && $option->value) {
            if (!\is_iterable($option->value)) {
                $option->value = (array)$option->value;
            }
            if ($option->value) {
                $instance->authors = [];
                foreach ($option->value as $index => $value) {
                    $instance->authors[$index] = Helper::toString($value);
                }
            }
        }
    },
    null, MockArticle::class
);

/**
 * Normalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithTitle $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'title' property
        $ret['title'] = null === $object->title ? null : (string)$object->title;
    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'title' nullable property
        $option = Helper::find($input, ['title'], $context);
        if ($option->success) {
            if (null === $option->value) {
                $instance->title = null;
            } else {
                $instance->title = Helper::toString($option->value);
            }
        }
    },
    null, MockWithTitle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'text' property
        $ret['text'] = null === $object->text ? null : \Generated8\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer);
    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'text' nullable property
        $option = Helper::find($input, ['text'], $context);
        if ($option->success) {
            if (null === $option->value) {
                $instance->text = null;
            } else {
                $instance->text = \Generated8\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::denormalize($option->value, $context, $denormalizer);
            }
        }
    },
    null, MockWithText::class
);
