<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use DateTimeImmutable;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Mock\MockArticle;
use MakinaCorpus\Normalizer\Mock\MockWithText;
use MakinaCorpus\Normalizer\Mock\MockWithTitle;
use MakinaCorpus\Normalizer\RuntimeHelper;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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
     * Normalize \MakinaCorpus\Normalizer\Mock\MockArticle instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\MockArticle instance.
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
        $ret['title'] = (null === $object->title ? null : ($normalizer ? $normalizer($object->title, $context, $normalizer) : $object->title));

    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        $instance->title = isset($input['title']) ? ($denormalizer ? $denormalizer('null', $input['title'], $context, $denormalizer) : $input['title']) : null;

    },
    null, MockWithTitle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {
        $ret['text'] = (null === $object->text ? null : ($normalizer ? $normalizer($object->text, $context, $normalizer) : $object->text));

    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        $instance->text = isset($input['text']) ? ($denormalizer ? $denormalizer('null', $input['text'], $context, $denormalizer) : $input['text']) : null;

    },
    null, MockWithText::class
);

/**
 * Normalizer for properties of MockArticle.
 */
MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, MockArticle $object, Context $context, ?callable $normalizer = null): void {

        $ret['id'] = (null === $object->id ? null : $object->id->__toString());

        $ret['createdAt'] = (null === $object->createdAt ? null : $object->createdAt->format('Y-m-d\\TH:i:sP'));

        $ret['updatedAt'] = (null === $object->updatedAt ? null : $object->updatedAt->format('Y-m-d\\TH:i:sP'));

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

        $ret['foo'] = (null === $object->foo ? null : (string)$object->foo);

        $ret['bar'] = (null === $object->bar ? null : (int)$object->bar);

        $ret['baz'] = (null === $object->baz ? null : (float)$object->baz);

        $ret['filename'] = (null === $object->filename ? null : (string)$object->filename);

    },
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        $instance->id = isset($input['id']) ? ($input['id'] instanceof UuidInterface
            ? $input['id']
            : Uuid::fromString($input['id'])
        ) : null;

        if (!isset($input['createdAt'])) {
            $context->nullValueError('DateTimeImmutable');
        } else {
            $instance->createdAt = ($input['createdAt'] instanceof DateTimeImmutable
                ? $input['createdAt']
                : RuntimeHelper::toDateImmutable($input['createdAt'], $context)
            );
        }

        $instance->updatedAt = isset($input['updatedAt']) ? ($input['updatedAt'] instanceof DateTimeImmutable
            ? $input['updatedAt']
            : RuntimeHelper::toDateImmutable($input['updatedAt'], $context)
        ) : null;

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
                        $instance->authors[$index] = RuntimeHelper::toString($value, $context);
                    }
                }
            }
        }

        $instance->foo = isset($input['foo']) ? RuntimeHelper::toString($input['foo'], $context) : null;

        if (!isset($input['bar'])) {
            $context->nullValueError('int');
        } else {
            $instance->bar = RuntimeHelper::toInt($input['bar'], $context);
        }

        if (!isset($input['baz'])) {
            $context->nullValueError('float');
        } else {
            $instance->baz = RuntimeHelper::toFloat($input['baz'], $context);
        }

        if (!isset($input['filename'])) {
            $context->nullValueError('string');
        } else {
            $instance->filename = RuntimeHelper::toString($input['filename'], $context);
        }

    },
    null, MockArticle::class
);

