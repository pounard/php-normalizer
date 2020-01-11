<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\Php74MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use DateTimeImmutable;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Mock\Php74MockArticle;
use MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat;
use MakinaCorpus\Normalizer\Mock\Php74MockWithText;
use MakinaCorpus\Normalizer\Mock\Php74MockWithTitle;
use MakinaCorpus\Normalizer\RuntimeHelper;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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
     * Normalize \MakinaCorpus\Normalizer\Mock\Php74MockArticle instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\Php74MockArticle instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
        $ret['title'] = (null === $object->title ? null : (string)$object->title);

    },
    null, Php74MockWithTitle::class
);

/**
 * Denormalizer for properties of Php74MockWithTitle.
 */
Php74MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        if (!isset($input['title'])) {
            $context->nullValueError('string');
        } else {
            $instance->title = RuntimeHelper::toString($input['title'], $context);
        }

    },
    null, Php74MockWithTitle::class
);

/**
 * Normalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, Php74MockWithText $object, Context $context, ?callable $normalizer = null): void {
        $ret['text'] = (null === $object->text ? null : Php74MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer));

    },
    null, Php74MockWithText::class
);

/**
 * Denormalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (Php74MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        $instance->text = isset($input['text']) ? ($input['text'] instanceof Php74MockTextWithFormat
            ? $input['text']
            : Php74MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer)
        ) : null;

    },
    null, Php74MockWithText::class
);

/**
 * Normalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, Php74MockArticle $object, Context $context, ?callable $normalizer = null): void {

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
    null, Php74MockArticle::class
);

/**
 * Denormalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (Php74MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

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
    null, Php74MockArticle::class
);

