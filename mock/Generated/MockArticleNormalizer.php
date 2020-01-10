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
use MakinaCorpus\Normalizer\Mock\MockTextWithFormat;
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
        try {
            $context->enter('title');
            $ret['title'] = (null === $object->title ? null : (string)$object->title);
        } finally {
            $context->leave();
        }

    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('title');
            $instance->title = isset($input['title']) ? RuntimeHelper::toString($input['title'], $context) : null;
        } finally {
            $context->leave();
        }

    },
    null, MockWithTitle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {
        try {
            $context->enter('text');
            $ret['text'] = (null === $object->text ? null : MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer));
        } finally {
            $context->leave();
        }

    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('text');
            $instance->text = isset($input['text']) ? ($input['text'] instanceof MockTextWithFormat
                ? $input['text']
                : MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer)
            ) : null;
        } finally {
            $context->leave();
        }

    },
    null, MockWithText::class
);

/**
 * Normalizer for properties of MockArticle.
 */
MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, MockArticle $object, Context $context, ?callable $normalizer = null): void {

        try {
            $context->enter('id');
            $ret['id'] = (null === $object->id ? null : $object->id->__toString());
        } finally {
            $context->leave();
        }

        try {
            $context->enter('createdAt');
            $ret['createdAt'] = (null === $object->createdAt ? null : $object->createdAt->format('Y-m-d\\TH:i:sP'));
        } finally {
            $context->leave();
        }

        try {
            $context->enter('updatedAt');
            $ret['updatedAt'] = (null === $object->updatedAt ? null : $object->updatedAt->format('Y-m-d\\TH:i:sP'));
        } finally {
            $context->leave();
        }

        try {
            $context->enter('authors');
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
        } finally {
            $context->leave();
        }

        try {
            $context->enter('foo');
            $ret['foo'] = (null === $object->foo ? null : (string)$object->foo);
        } finally {
            $context->leave();
        }

        try {
            $context->enter('bar');
            $ret['bar'] = (null === $object->bar ? null : (int)$object->bar);
        } finally {
            $context->leave();
        }

        try {
            $context->enter('baz');
            $ret['baz'] = (null === $object->baz ? null : (float)$object->baz);
        } finally {
            $context->leave();
        }

        try {
            $context->enter('filename');
            $ret['filename'] = (null === $object->filename ? null : (string)$object->filename);
        } finally {
            $context->leave();
        }

    },
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        try {
            $context->enter('id');
            $instance->id = isset($input['id']) ? ($input['id'] instanceof UuidInterface
                ? $input['id']
                : Uuid::fromString($input['id'])
            ) : null;
        } finally {
            $context->leave();
        }

        try {
            $context->enter('createdAt');
            if (!isset($input['createdAt'])) {
                $context->nullValueError('DateTimeImmutable');
            } else {
                $instance->createdAt = ($input['createdAt'] instanceof DateTimeImmutable
                    ? $input['createdAt']
                    : RuntimeHelper::toDateImmutable($input['createdAt'], $context)
                );
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('updatedAt');
            $instance->updatedAt = isset($input['updatedAt']) ? ($input['updatedAt'] instanceof DateTimeImmutable
                ? $input['updatedAt']
                : RuntimeHelper::toDateImmutable($input['updatedAt'], $context)
            ) : null;
        } finally {
            $context->leave();
        }

        try {
            $context->enter('authors');
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
        } finally {
            $context->leave();
        }

        try {
            $context->enter('foo');
            $instance->foo = isset($input['foo']) ? RuntimeHelper::toString($input['foo'], $context) : null;
        } finally {
            $context->leave();
        }

        try {
            $context->enter('bar');
            if (!isset($input['bar'])) {
                $context->nullValueError('int');
            } else {
                $instance->bar = RuntimeHelper::toInt($input['bar'], $context);
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('baz');
            if (!isset($input['baz'])) {
                $context->nullValueError('float');
            } else {
                $instance->baz = RuntimeHelper::toFloat($input['baz'], $context);
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('filename');
            if (!isset($input['filename'])) {
                $context->nullValueError('string');
            } else {
                $instance->filename = RuntimeHelper::toString($input['filename'], $context);
            }
        } finally {
            $context->leave();
        }

    },
    null, MockArticle::class
);

