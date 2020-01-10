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
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Mock\Php74MockArticle;
use MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat;
use MakinaCorpus\Normalizer\Mock\Php74MockWithText;
use MakinaCorpus\Normalizer\Mock\Php74MockWithTitle;
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
        try {
            $context->enter('title');
            $ret['title'] = (null === $object->title ? null : (string)$object->title);
        } finally {
            $context->leave();
        }

    },
    null, Php74MockWithTitle::class
);

/**
 * Denormalizer for properties of Php74MockWithTitle.
 */
Php74MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('title');
            if (!isset($input['title'])) {
                $context->nullValueError('string');
            } else {
                $instance->title = Helper::toString($input['title'], $context);
            }
        } finally {
            $context->leave();
        }

    },
    null, Php74MockWithTitle::class
);

/**
 * Normalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, Php74MockWithText $object, Context $context, ?callable $normalizer = null): void {
        try {
            $context->enter('text');
            $ret['text'] = (null === $object->text ? null : Php74MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer));
        } finally {
            $context->leave();
        }

    },
    null, Php74MockWithText::class
);

/**
 * Denormalizer for properties of Php74MockWithText.
 */
Php74MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (Php74MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('text');
            $instance->text = isset($input['text']) ? ($input['text'] instanceof Php74MockTextWithFormat
                ? $input['text']
                : Php74MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer)
            ) : null;
        } finally {
            $context->leave();
        }

    },
    null, Php74MockWithText::class
);

/**
 * Normalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$normalizer2 = \Closure::bind(
    static function (array &$ret, Php74MockArticle $object, Context $context, ?callable $normalizer = null): void {

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
    null, Php74MockArticle::class
);

/**
 * Denormalizer for properties of Php74MockArticle.
 */
Php74MockArticleNormalizer::$denormalizer2 = \Closure::bind(
    static function (Php74MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

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
                    : Helper::toDateImmutable($input['createdAt'], $context)
                );
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('updatedAt');
            $instance->updatedAt = isset($input['updatedAt']) ? ($input['updatedAt'] instanceof DateTimeImmutable
                ? $input['updatedAt']
                : Helper::toDateImmutable($input['updatedAt'], $context)
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
                            $instance->authors[$index] = Helper::toString($value, $context);
                        }
                    }
                }
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('foo');
            $instance->foo = isset($input['foo']) ? Helper::toString($input['foo'], $context) : null;
        } finally {
            $context->leave();
        }

        try {
            $context->enter('bar');
            if (!isset($input['bar'])) {
                $context->nullValueError('int');
            } else {
                $instance->bar = Helper::toInt($input['bar'], $context);
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('baz');
            if (!isset($input['baz'])) {
                $context->nullValueError('float');
            } else {
                $instance->baz = Helper::toFloat($input['baz'], $context);
            }
        } finally {
            $context->leave();
        }

        try {
            $context->enter('filename');
            if (!isset($input['filename'])) {
                $context->nullValueError('string');
            } else {
                $instance->filename = Helper::toString($input['filename'], $context);
            }
        } finally {
            $context->leave();
        }

    },
    null, Php74MockArticle::class
);

