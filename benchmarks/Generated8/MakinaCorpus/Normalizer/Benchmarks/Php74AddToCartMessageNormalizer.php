<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated8\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;

/**
 * Public implementation of (de)normalizer for class Php74AddToCartMessage.
 */
final class Php74AddToCartMessageNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage instances.
     *
     * @param callable $normalizer
     *   A callback that will normalize externally handled values, parameters are:
     *      - mixed $input raw value from denormalized data
     *      - Context $context the context
     */
    public static function normalize($object, Context $context, ?callable $normalizer = null): array
    {
        $ret = [];

        (self::$normalizer0)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): Php74AddToCartMessage
    {
        $ret = (new \ReflectionClass(Php74AddToCartMessage::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of Php74AddToCartMessage.
 */
Php74AddToCartMessageNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, Php74AddToCartMessage $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'orderId' property
        $ret['orderId'] = null === $object->orderId ? null : $object->orderId->__toString();

        // Normalize 'productId' property
        $ret['productId'] = null === $object->productId ? null : (int)$object->productId;

        // Normalize 'amount' property
        $ret['amount'] = null === $object->amount ? null : (float)$object->amount;
    },
    null, Php74AddToCartMessage::class
);

/**
 * Denormalizer for properties of Php74AddToCartMessage.
 */
Php74AddToCartMessageNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74AddToCartMessage $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'orderId' nullable property
        $instance->orderId = isset($input['orderId']) ? \Ramsey\Uuid\Uuid::fromString($input['orderId']) : null;

        // Denormalize 'productId' required property
        if (!isset($input['productId'])) {
            $context->addError(\sprintf("'%s' cannot be null", 'productId'), $context);
        } else {
            $instance->productId = Helper::toInt($input['productId']);
        }

        // Denormalize 'amount' required property
        if (!isset($input['amount'])) {
            $context->addError(\sprintf("'%s' cannot be null", 'amount'), $context);
        } else {
            $instance->amount = Helper::toFloat($input['amount']);
        }
    },
    null, Php74AddToCartMessage::class
);
