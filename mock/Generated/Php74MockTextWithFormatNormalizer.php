<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat;

final class Php74MockTextWithFormatNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): Php74MockTextWithFormat
    {
        $ret = (new \ReflectionClass(Php74MockTextWithFormat::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of Php74MockTextWithFormat.
 */
Php74MockTextWithFormatNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, Php74MockTextWithFormat $object, Context $context, ?callable $normalizer = null): void {

        $ret['text'] = (null === $object->text ? null : (string)$object->text);

        $ret['format'] = (null === $object->format ? null : (string)$object->format);
    },
    null, Php74MockTextWithFormat::class
);

/**
 * Denormalizer for properties of Php74MockTextWithFormat.
 */
Php74MockTextWithFormatNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74MockTextWithFormat $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        $instance->text = isset($input['text']) ? Helper::toString($input['text'], $context) : null;

        if (!isset($input['format'])) {
            $context->nullValueError('string');
        } else {
            $instance->format = Helper::toString($input['format'], $context);
        }
    },
    null, Php74MockTextWithFormat::class
);
