<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithUuid.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Generated\Mock;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithUuid;

final class MockClassWithUuidNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /**
     * Normalize \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithUuid instance into an array.
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
     * Create and denormalize an \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithUuid instance.
     *
     * @param callable $normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockClassWithUuid
    {
        $ret = (new \ReflectionClass(MockClassWithUuid::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockClassWithUuid.
 */
MockClassWithUuidNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockClassWithUuid $object, Context $context, ?callable $normalizer = null): void {
        // Normalize 'uuid' property
        $ret['uuid'] = (null === $object->uuid ? null : $object->uuid->__toString());
    },
    null, MockClassWithUuid::class
);

/**
 * Denormalizer for properties of MockClassWithUuid.
 */
MockClassWithUuidNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockClassWithUuid $instance, array $input, Context $context, ?callable $denormalizer = null): void {
        // Denormalize 'uuid' nullable property
        $instance->uuid = isset($input['uuid']) ? ($input['uuid'] instanceof \Ramsey\Uuid\UuidInterface
            ? $input['uuid']
            : \Ramsey\Uuid\Uuid::fromString($input['uuid'])
        ) : null;
    },
    null, MockClassWithUuid::class
);
