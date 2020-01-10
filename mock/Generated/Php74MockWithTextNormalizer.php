<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\Php74MockWithText.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat;
use MakinaCorpus\Normalizer\Mock\Php74MockWithText;
use MakinaCorpus\Normalizer\Mock\Php74MockWithTitle;
use MakinaCorpus\Normalizer\RuntimeHelper;

final class Php74MockWithTextNormalizer
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
     * Normalize \MakinaCorpus\Normalizer\Mock\Php74MockWithText instance into an array.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::normalize()
     */
    public static function normalize($object, Context $context, ?callable $normalizer = null): array
    {
        $ret = [];
        (self::$normalizer0)($ret, $object, $context, $normalizer);
        (self::$normalizer1)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\Php74MockWithText instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): Php74MockWithText
    {
        $ret = (new \ReflectionClass(Php74MockWithText::class))->newInstanceWithoutConstructor();
        (self::$denormalizer0)($ret, $input, $context, $denormalizer);
        (self::$denormalizer1)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of Php74MockWithTitle.
 */
Php74MockWithTextNormalizer::$normalizer0 = \Closure::bind(
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
Php74MockWithTextNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        try {
            $context->enter('title');
            if (!isset($input['title'])) {
                $context->nullValueError('string');
            } else {
                $instance->title = RuntimeHelper::toString($input['title'], $context);
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
Php74MockWithTextNormalizer::$normalizer1 = \Closure::bind(
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
Php74MockWithTextNormalizer::$denormalizer1 = \Closure::bind(
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

