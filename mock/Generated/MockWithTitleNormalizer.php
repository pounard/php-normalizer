<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\MockWithTitle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Mock\MockWithTitle;

final class MockWithTitleNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Mock\MockWithTitle instance into an array.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::normalize()
     */
    public static function normalize($object, Context $context, ?callable $normalizer = null): array
    {
        $ret = [];
        (self::$normalizer0)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\MockWithTitle instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockWithTitle
    {
        $ret = (new \ReflectionClass(MockWithTitle::class))->newInstanceWithoutConstructor();
        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockWithTitle.
 */
MockWithTitleNormalizer::$normalizer0 = \Closure::bind(
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
MockWithTitleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
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
    null, MockWithTitle::class
);

