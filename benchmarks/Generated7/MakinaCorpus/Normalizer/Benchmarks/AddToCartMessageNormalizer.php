<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated7\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer as Helper;
use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage;
use MakinaCorpus\Normalizer\Context;

/**
 * Public implementation of (de)normalizer for class AddToCartMessage.
 */
final class AddToCartMessageNormalizer
{
    /** @var callable */
    public static $denormalizer0;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage instances.
     *
     * @param callable $normalizer
     *   A callback that will hydrate externally handled values, parameters are:
     *      - string $type PHP native type to hydrate
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $normalizer = null): AddToCartMessage
    {
        $ret = (new \ReflectionClass(AddToCartMessage::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $normalizer);

        return $ret;
    }
}

/**
 * Denormalizer for properties of AddToCartMessage.
 */
AddToCartMessageNormalizer::$denormalizer0 = \Closure::bind(
    static function (AddToCartMessage $instance, array $input, Context $context, ?callable $normalizer = null): void {

        // Denormalize 'orderId' property
        $value = Helper\find_value($input, ['orderId'], $context);
        if (null !== $value && $normalizer) {
            $value = $normalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
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
