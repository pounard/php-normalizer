<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Normalizer\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage;
use MakinaCorpus\Normalizer\Context;

final class AddToCartMessageNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): AddToCartMessage
    {
        $ret = (new \ReflectionClass(AddToCartMessage::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of AddToCartMessage.
 */
AddToCartMessageNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, AddToCartMessage $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'orderId' property
        $ret['orderId'] = (null === $object->orderId ? null : $object->orderId->__toString());

        // Normalize 'productId' property
        $ret['productId'] = (null === $object->productId ? null : (int)$object->productId);

        // Normalize 'amount' property
        $ret['amount'] = (null === $object->amount ? null : (float)$object->amount);
    },
    null, AddToCartMessage::class
);

/**
 * Denormalizer for properties of AddToCartMessage.
 */
AddToCartMessageNormalizer::$denormalizer0 = \Closure::bind(
    static function (AddToCartMessage $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'orderId' nullable property
        $instance->orderId = isset($input['orderId']) ? ($input['orderId'] instanceof \Ramsey\Uuid\UuidInterface
            ? $input['orderId']
            : \Ramsey\Uuid\Uuid::fromString($input['orderId'])
        ) : null;

        // Denormalize 'productId' required property
        if (!isset($input['productId'])) {
            $context->nullValueError('int');
        } else {
            $instance->productId = \MakinaCorpus\Normalizer\Helper::toInt($input['productId'], $context);
        }

        // Denormalize 'amount' required property
        if (!isset($input['amount'])) {
            $context->nullValueError('float');
        } else {
            $instance->amount = \MakinaCorpus\Normalizer\Helper::toFloat($input['amount'], $context);
        }
    },
    null, AddToCartMessage::class
);
