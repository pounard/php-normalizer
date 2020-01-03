<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\Php74MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated8\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\Php74MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\Php74MockWithText;
use MakinaCorpus\Normalizer\Benchmarks\Php74MockWithTitle;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;

/**
 * Public implementation of (de)normalizer for class Php74MockArticle.
 */
final class Php74MockArticleNormalizer
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
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\Php74MockArticle instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Benchmarks\Php74MockArticle instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): Php74MockArticle
    {
        $ret = (new \ReflectionClass(Php74MockArticle::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);
        (self::$denormalizer1)($ret, $input, $context, $denormalizer);
        (self::$denormalizer2)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of Php74MockWithTitle.
 */
Php74MockArticleNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, Php74MockWithTitle $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'title' property
        $ret['title'] = null === $object->title ? null : (string)$object->title;
    },
    null, Php74MockWithTitle::class
);

/**
 * Denormalizer for properties of Php74MockWithTitle.
 */
Php74MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'title' nullable property
        $instance->title = isset($input['title']) ? Helper::toString($input['title']) : null;
    },
    null, Php74MockWithTitle::class
);

/**
 * Normalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, Php74MockWithText $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'text' property
        $ret['text'] = null === $object->text ? null : \Generated8\MakinaCorpus\Normalizer\Benchmarks\Php74MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer);
    },
    null, Php74MockWithText::class
);

/**
 * Denormalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (Php74MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'text' nullable property
        $instance->text = isset($input['text']) ? \Generated8\MakinaCorpus\Normalizer\Benchmarks\Php74MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer) : null;
    },
    null, Php74MockWithText::class
);

/**
 * Normalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, Php74MockArticle $object, Context $context, ?callable $normalizer = null): void {

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

        // Normalize 'foo' property
        $ret['foo'] = null === $object->foo ? null : (string)$object->foo;

        // Normalize 'bar' property
        $ret['bar'] = null === $object->bar ? null : (int)$object->bar;

        // Normalize 'baz' property
        $ret['baz'] = null === $object->baz ? null : (float)$object->baz;

        // Normalize 'filename' property
        $ret['filename'] = null === $object->filename ? null : (string)$object->filename;
    },
    null, Php74MockArticle::class
);

/**
 * Denormalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (Php74MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'id' nullable property
        $instance->id = isset($input['id']) ? $denormalizer ? $denormalizer(\Ramsey\Uuid\UuidInterface::class, $input['id'], $context, $denormalizer) : $input['id'] : null;

        // Denormalize 'createdAt' required property
        if (!isset($input['createdAt'])) {
            Helper::error(\sprintf("'%s' cannot be null", 'createdAt'), $context);
        } else {
            $instance->createdAt = $denormalizer ? $denormalizer(\DateTimeImmutable::class, $input['createdAt'], $context, $denormalizer) : $input['createdAt'];
        }

        // Denormalize 'updatedAt' nullable property
        $instance->updatedAt = isset($input['updatedAt']) ? $denormalizer ? $denormalizer(\DateTimeImmutable::class, $input['updatedAt'], $context, $denormalizer) : $input['updatedAt'] : null;

        // Denormalize 'authors' collection property
        if (isset($input['authors'])) {
            if (!\is_iterable($input['authors'])) {
                $input['authors'] = (array)$input['authors'];
            }
            if ($input['authors']) {
                $instance->authors = [];
                foreach ($input['authors'] as $index => $value) {
                    if (null === $value) {
                        Helper::error("Property value in collection cannot be null");
                        $instance->authors[$index] = null;
                    } else {
                        $instance->authors[$index] = Helper::toString($value);
                    }
                }
            }
        }

        // Denormalize 'foo' nullable property
        $instance->foo = isset($input['foo']) ? Helper::toString($input['foo']) : null;

        // Denormalize 'bar' required property
        if (!isset($input['bar'])) {
            Helper::error(\sprintf("'%s' cannot be null", 'bar'), $context);
        } else {
            $instance->bar = Helper::toInt($input['bar']);
        }

        // Denormalize 'baz' required property
        if (!isset($input['baz'])) {
            Helper::error(\sprintf("'%s' cannot be null", 'baz'), $context);
        } else {
            $instance->baz = Helper::toFloat($input['baz']);
        }

        // Denormalize 'filename' required property
        if (!isset($input['filename'])) {
            Helper::error(\sprintf("'%s' cannot be null", 'filename'), $context);
        } else {
            $instance->filename = Helper::toString($input['filename']);
        }
    },
    null, Php74MockArticle::class
);
