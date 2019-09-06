<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated2\MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage;
use MakinaCorpus\Normalizer\Context;

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
        $value = self::find('orderId', $input, ['orderId'], $context);
        if (null === $value) {
            $context->addError("Property 'orderId' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('Ramsey\\Uuid\\UuidInterface', $value, $context);
        }
        if (!$value instanceof \Ramsey\Uuid\UuidInterface) {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'orderId', $value);

        // Denormalize 'productId' property
        $value = self::find('productId', $input, ['productId'], $context);
        if (null === $value) {
            $context->addError("Property 'productId' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('int', $value, $context);
        }
        if (!\MakinaCorpus\Normalizer\gettype_real($value) === 'int') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'productId', $value);

        // Denormalize 'amount' property
        $value = self::find('amount', $input, ['amount'], $context);
        if (null === $value) {
            $context->addError("Property 'amount' cannot be null");
        }
        if (null !== $value && $normalizer) {
            $value = $normalizer('float', $value, $context);
        }
        if (!\MakinaCorpus\Normalizer\gettype_real($value) === 'float') {
            $value = null;
        }
        \call_user_func(self::$accessor, $ret, 'amount', $value);

        return $ret;
    }

    /**
     * Find value matching in array
     */
    private static function find(string $propName, array $input, array $names, Context $context)
    {
        $found = $value = null;
        foreach ($names as $name) {
            if (\array_key_exists($name, $input)) {
                if ($found) {
                    $context->addError(\sprintf("Property '%s' found in '%s' but was already found in '%s'", $propName, $found, $name));
                } else {
                    $found = $name;
                    $value = $input[$name];
                }
            }
        }
        return $value;
    }
}

AddToCartMessageNormalizer::$accessor = \Closure::bind(
    static function (AddToCartMessage $instance, string $propName, $value): void {
        $instance->{$propName} = $value;
    },
    null, AddToCartMessage::class
);
