<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated5\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage;
use MakinaCorpus\Normalizer\Context;

use MakinaCorpus\Normalizer as Helper;

final class AddToCartMessageNormalizer
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static $accessor;

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

        // Denormalize 'orderId' property
        $value = Helper\find_value($input, ['orderId'], $context);
        $value = Helper\to_int($value, $context);
        \call_user_func(self::$accessor, $ret, 'orderId', $value);

        // Denormalize 'productId' property
        $value = Helper\find_value($input, ['productId'], $context);
        $value = Helper\to_int($value, $context);
        \call_user_func(self::$accessor, $ret, 'productId', $value);

        // Denormalize 'amount' property
        $value = Helper\find_value($input, ['amount'], $context);
        $value = Helper\to_int($value, $context);
        \call_user_func(self::$accessor, $ret, 'amount', $value);

        return $ret;
    }
}

AddToCartMessageNormalizer::$accessor = \Closure::bind(
    static function (AddToCartMessage $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, AddToCartMessage::class
);
