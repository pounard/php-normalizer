<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Mock\MockTextWithFormat.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Mock\Generated;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Mock\MockTextWithFormat;
use MakinaCorpus\Normalizer\RuntimeHelper;

final class MockTextWithFormatNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Mock\MockTextWithFormat instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Mock\MockTextWithFormat instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockTextWithFormat
    {
        $ret = (new \ReflectionClass(MockTextWithFormat::class))->newInstanceWithoutConstructor();
        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockTextWithFormat.
 */
MockTextWithFormatNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockTextWithFormat $object, Context $context, ?callable $normalizer = null): void {

        $ret['text'] = (null === $object->text ? null : (string)$object->text);

        $ret['format'] = (null === $object->format ? null : (string)$object->format);

    },
    null, MockTextWithFormat::class
);

/**
 * Denormalizer for properties of MockTextWithFormat.
 */
MockTextWithFormatNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockTextWithFormat $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        $instance->text = isset($input['text']) ? RuntimeHelper::toString($input['text'], $context) : null;

        if (!isset($input['format'])) {
            $context->nullValueError('string');
        } else {
            $instance->format = RuntimeHelper::toString($input['format'], $context);
        }

    },
    null, MockTextWithFormat::class
);

