<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\MockWithText.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Mock\MockTextWithFormat;
use MakinaCorpus\Normalizer\Mock\MockWithText;
use MakinaCorpus\Normalizer\Mock\MockWithTitle;

final class MockWithTextNormalizer
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
     * Normalize \MakinaCorpus\Normalizer\Mock\MockWithText instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\MockWithText instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockWithText
    {
        $ret = (new \ReflectionClass(MockWithText::class))->newInstanceWithoutConstructor();
        (self::$denormalizer0)($ret, $input, $context, $denormalizer);
        (self::$denormalizer1)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockWithTitle.
 */
MockWithTextNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockWithTitle $object, Context $context, ?callable $normalizer = null): void {
        $ret['title'] = (null === $object->title ? null : ($normalizer ? $normalizer($object->title, $context, $normalizer) : $object->title));

    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockWithTextNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        $instance->title = isset($input['title']) ? ($denormalizer ? $denormalizer('null', $input['title'], $context, $denormalizer) : $input['title']) : null;

    },
    null, MockWithTitle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockWithTextNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {
        $ret['text'] = (null === $object->text ? null : MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer));

    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockWithTextNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        $instance->text = isset($input['text']) ? ($input['text'] instanceof MockTextWithFormat
            ? $input['text']
            : MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer)
        ) : null;

    },
    null, MockWithText::class
);

