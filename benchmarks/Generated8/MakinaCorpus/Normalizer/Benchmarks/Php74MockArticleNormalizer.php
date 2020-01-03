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
        $ret['title'] = (null === $object->title ? null : (string)$object->title);
    },
    null, Php74MockWithTitle::class
);

/**
 * Denormalizer for properties of Php74MockWithTitle.
 */
Php74MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'title' required property
        if (!isset($input['title'])) {
            $context->nullValueError('string');
        } else {
            $instance->title = \MakinaCorpus\Normalizer\Helper::toString($input['title'], $context);
        }
    },
    null, Php74MockWithTitle::class
);

/**
 * Normalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, Php74MockWithText $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'text' property
        $ret['text'] = (null === $object->text ? null : \Generated8\MakinaCorpus\Normalizer\Benchmarks\Php74MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer));
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
        $ret['id'] = (null === $object->id ? null : $object->id->__toString());

        // Normalize 'createdAt' property
        $ret['createdAt'] = (null === $object->createdAt ? null : ($normalizer ? $normalizer($object->createdAt, $context, $normalizer) : $object->createdAt));

        // Normalize 'updatedAt' property
        $ret['updatedAt'] = (null === $object->updatedAt ? null : ($normalizer ? $normalizer($object->updatedAt, $context, $normalizer) : $object->updatedAt));

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
        $ret['foo'] = (null === $object->foo ? null : (string)$object->foo);

        // Normalize 'bar' property
        $ret['bar'] = (null === $object->bar ? null : (int)$object->bar);

        // Normalize 'baz' property
        $ret['baz'] = (null === $object->baz ? null : (float)$object->baz);

        // Normalize 'filename' property
        $ret['filename'] = (null === $object->filename ? null : (string)$object->filename);
    },
    null, Php74MockArticle::class
);

/**
 * Denormalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (Php74MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'id' nullable property
        $instance->id = isset($input['id']) ? \Ramsey\Uuid\Uuid::fromString($input['id']) : null;

        // Denormalize 'createdAt' required property
        if (!isset($input['createdAt'])) {
            $context->nullValueError('DateTimeImmutable');
        } else {
            $instance->createdAt = ($denormalizer ? $denormalizer(\DateTimeImmutable::class, $input['createdAt'], $context, $denormalizer) : $input['createdAt']);
        }

        // Denormalize 'updatedAt' nullable property
        $instance->updatedAt = isset($input['updatedAt']) ? ($denormalizer ? $denormalizer(\DateTimeImmutable::class, $input['updatedAt'], $context, $denormalizer) : $input['updatedAt']) : null;

        // Denormalize 'authors' collection property
        if (isset($input['authors'])) {
            if (!\is_iterable($input['authors'])) {
                $input['authors'] = (array)$input['authors'];
            }
            if ($input['authors']) {
                $instance->authors = [];
                foreach ($input['authors'] as $index => $value) {
                    if (null === $value) {
                        $context->nullValueError('string');
                        $instance->authors[$index] = null;
                    } else {
                        $instance->authors[$index] = \MakinaCorpus\Normalizer\Helper::toString($value, $context);
                    }
                }
            }
        }

        // Denormalize 'foo' nullable property
        $instance->foo = isset($input['foo']) ? \MakinaCorpus\Normalizer\Helper::toString($input['foo'], $context) : null;

        // Denormalize 'bar' required property
        if (!isset($input['bar'])) {
            $context->nullValueError('int');
        } else {
            $instance->bar = \MakinaCorpus\Normalizer\Helper::toInt($input['bar'], $context);
        }

        // Denormalize 'baz' required property
        if (!isset($input['baz'])) {
            $context->nullValueError('float');
        } else {
            $instance->baz = \MakinaCorpus\Normalizer\Helper::toFloat($input['baz'], $context);
        }

        // Denormalize 'filename' required property
        if (!isset($input['filename'])) {
            $context->nullValueError('string');
        } else {
            $instance->filename = \MakinaCorpus\Normalizer\Helper::toString($input['filename'], $context);
        }
    },
    null, Php74MockArticle::class
);
