<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\MockWithTitle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Normalizer\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle;
use MakinaCorpus\Normalizer\Context;

final class MockWithTitleNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Benchmarks\MockWithTitle instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Benchmarks\MockWithTitle instance.
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
        // Normalize 'title' property
        $ret['title'] = (null === $object->title ? null : (string)$object->title);
    },
    null, MockWithTitle::class
);

/**
 * Denormalizer for properties of MockWithTitle.
 */
MockWithTitleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockWithTitle $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'title' required property
        if (!isset($input['title'])) {
            $context->nullValueError('string');
        } else {
            $instance->title = \MakinaCorpus\Normalizer\Helper::toString($input['title'], $context);
        }
    },
    null, MockWithTitle::class
);
