<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Functional\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated8\MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Functional\MockArticle;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;

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

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Functional\MockArticle instances.
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

        return $ret;
    }

    /**
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Functional\MockArticle instances.
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

        // Normalize 'createdAt' property
        $ret['createdAt'] = null === $object->createdAt ? null : $normalizer ? $normalizer($object->createdAt, $context, $normalizer) : $object->createdAt;

        // Normalize 'updatedAt' property
        $ret['updatedAt'] = null === $object->updatedAt ? null : $normalizer ? $normalizer($object->updatedAt, $context, $normalizer) : $object->updatedAt;

        // Normalize 'authors' property
        $ret['authors'] = [];
        if ($object->authors) {
            foreach ($object->authors as $index => $value) {
                if (null === $value) {
                    $ret['authors'][$index] = null;
                } else {
                    $ret['authors'][$index] = (string)$value;
                }
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
        if (!$option->success || null === $option->value) {
            Helper::error(\sprintf("'%s' cannot be null", 'id'), $context);
        } else {
            $instance->id = $denormalizer ? $denormalizer(\Ramsey\Uuid\UuidInterface::class, $option->value, $context, $denormalizer) : $option->value;
        }

        // Denormalize 'createdAt' required property
        $option = Helper::find($input, ['createdAt'], $context);
        if (!$option->success || null === $option->value) {
            Helper::error(\sprintf("'%s' cannot be null", 'createdAt'), $context);
        } else {
            $instance->createdAt = $denormalizer ? $denormalizer(\DateTimeInterface::class, $option->value, $context, $denormalizer) : $option->value;
        }

        // Denormalize 'updatedAt' required property
        $option = Helper::find($input, ['updatedAt'], $context);
        if (!$option->success || null === $option->value) {
            Helper::error(\sprintf("'%s' cannot be null", 'updatedAt'), $context);
        } else {
            $instance->updatedAt = $denormalizer ? $denormalizer(\DateTimeInterface::class, $option->value, $context, $denormalizer) : $option->value;
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
                    if (null === $value) {
                        Helper::error("Property value in collection cannot be null");
                        $instance->authors[$index] = null;
                    } else {
                        $instance->authors[$index] = Helper::toString($value);
                    }
                }
            }
        }
    },
    null, MockArticle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'title' property
        $ret['title'] = null === $object->title ? null : (string)$object->title;

        // Normalize 'text' property
        $ret['text'] = null === $object->text ? null : \Generated8\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer);
    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'title' nullable property
        $option = Helper::find($input, ['title'], $context);
        if ($option->success) {
            if (null === $option->value) {
                $instance->title = null;
            } else {
                $instance->title = Helper::toString($option->value);
            }
        }

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
