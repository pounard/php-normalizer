<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\Php74MockWithTitle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Mock\Php74MockWithTitle;
use MakinaCorpus\Normalizer\RuntimeHelper;

final class Php74MockWithTitleNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Mock\Php74MockWithTitle instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\Php74MockWithTitle instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): Php74MockWithTitle
    {
        $ret = (new \ReflectionClass(Php74MockWithTitle::class))->newInstanceWithoutConstructor();
        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of Php74MockWithTitle.
 */
Php74MockWithTitleNormalizer::$normalizer0 = \Closure::bind(
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
Php74MockWithTitleNormalizer::$denormalizer0 = \Closure::bind(
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

