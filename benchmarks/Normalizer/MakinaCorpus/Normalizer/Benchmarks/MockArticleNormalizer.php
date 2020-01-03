<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Normalizer\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\MockWithText;
use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle;
use MakinaCorpus\Normalizer\Context;

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
     * Normalize \MakinaCorpus\Normalizer\Benchmarks\MockArticle instance into an array.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::normalize()
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Benchmarks\MockArticle instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        // Normalize 'title' property
        $ret['title'] = (null === $object->title ? null : (string)$object->title);
    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'title' nullable property
        $instance->title = isset($input['title']) ? \MakinaCorpus\Normalizer\Helper::toString($input['title'], $context) : null;
    },
    null, MockWithTitle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'text' property
        $ret['text'] = (null === $object->text ? null : \Normalizer\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer));
    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'text' nullable property
        $instance->text = isset($input['text']) ? ($input['text'] instanceof \MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat
            ? $input['text']
            : \Normalizer\MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer)
        ) : null;
    },
    null, MockWithText::class
);

/**
 * Normalizer for properties of MockArticle.
 */
MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, MockArticle $object, Context $context, ?callable $normalizer = null): void {

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
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'id' nullable property
        $instance->id = isset($input['id']) ? ($input['id'] instanceof \Ramsey\Uuid\UuidInterface
            ? $input['id']
            : \Ramsey\Uuid\Uuid::fromString($input['id'])
        ) : null;

        // Denormalize 'createdAt' required property
        if (!isset($input['createdAt'])) {
            $context->nullValueError('DateTimeImmutable');
        } else {
            $instance->createdAt = ($input['createdAt'] instanceof \DateTimeImmutable
                ? $input['createdAt']
                : ($denormalizer ? $denormalizer('DateTimeImmutable', $input['createdAt'], $context, $denormalizer) : $input['createdAt'])
            );
        }

        // Denormalize 'updatedAt' nullable property
        $instance->updatedAt = isset($input['updatedAt']) ? ($input['updatedAt'] instanceof \DateTimeImmutable
            ? $input['updatedAt']
            : ($denormalizer ? $denormalizer('DateTimeImmutable', $input['updatedAt'], $context, $denormalizer) : $input['updatedAt'])
        ) : null;

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
    null, MockArticle::class
);
