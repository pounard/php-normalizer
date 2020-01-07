<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Normalizer\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Php74AddToCartMessageNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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

        $ret['orderId'] = (null === $object->orderId ? null : $object->orderId->__toString());

        $ret['productId'] = (null === $object->productId ? null : (int)$object->productId);

        $ret['amount'] = (null === $object->amount ? null : (float)$object->amount);
    },
    null, Php74AddToCartMessage::class
);

/**
 * Denormalizer for properties of Php74AddToCartMessage.
 */
Php74AddToCartMessageNormalizer::$denormalizer0 = \Closure::bind(
    static function (Php74AddToCartMessage $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        $instance->orderId = isset($input['orderId']) ? ($input['orderId'] instanceof UuidInterface
            ? $input['orderId']
            : Uuid::fromString($input['orderId'])
        ) : null;

        if (!isset($input['productId'])) {
            $context->nullValueError('int');
        } else {
            $instance->productId = Helper::toInt($input['productId'], $context);
        }

        if (!isset($input['amount'])) {
            $context->nullValueError('float');
        } else {
            $instance->amount = Helper::toFloat($input['amount'], $context);
        }
    },
    null, Php74AddToCartMessage::class
);
