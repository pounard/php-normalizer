<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated7\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Generator\Iterations as Helper;

/**
 * Public implementation of (de)normalizer for class AddToCartMessage.
 */
final class AddToCartMessageNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage instances.
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
     * Create and denormalize MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
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

        // Denormalize 'orderId' property
        $value = $object->orderId;
        if (null !== $value && $normalizer) {
            $value = $normalizer($value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'orderId' cannot be null", $context);
            }
        }
        $ret['orderId'] = $value;

        // Denormalize 'productId' property
        $value = $object->productId;
        $value = Helper\to_int($value, $context);
        $ret['productId'] = $value;

        // Denormalize 'amount' property
        $value = $object->amount;
        $value = Helper\to_float($value, $context);
        $ret['amount'] = $value;
    },
    null, AddToCartMessage::class
);

/**
 * Denormalizer for properties of AddToCartMessage.
 */
AddToCartMessageNormalizer::$denormalizer0 = \Closure::bind(
    static function (AddToCartMessage $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'orderId' property
        $value = Helper\find_value($input, ['orderId'], $context);
        if (null !== $value && $denormalizer) {
            $value = $denormalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
            if (null === $value) {
                Helper\handle_error("Property 'orderId' cannot be null", $context);
            } else if (!($value instanceof \Ramsey\Uuid\UuidInterface)) {
                Helper\handle_error("Type mismatch", $context);
                $value = null;
            }
        }
        $instance->orderId = $value;

        // Denormalize 'productId' property
        $value = Helper\find_value($input, ['productId'], $context);
        $value = Helper\to_int($value, $context);
        $instance->productId = $value;

        // Denormalize 'amount' property
        $value = Helper\find_value($input, ['amount'], $context);
        $value = Helper\to_float($value, $context);
        $instance->amount = $value;
    },
    null, AddToCartMessage::class
);
