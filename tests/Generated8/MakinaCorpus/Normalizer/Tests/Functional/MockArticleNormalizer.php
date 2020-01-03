<?php
/**
 * Generated (de)normalizer for class MakinaCorpus\Normalizer\Tests\Functional\MockArticle.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace Generated8\MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;
use MakinaCorpus\Normalizer\Tests\Functional\MockArticle;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;

/**
 * Public implementation of (de)normalizer for class MockArticle.
 */
final class MockArticleNormalizer
{
    /** @var callable */
    public static $normalizer0;

    /** @var callable */
    public static $denormalizer0;

    /** @var callable */
    public static $normalizer1;

    /** @var callable */
    public static $denormalizer1;

    /**
     * Create and normalize MakinaCorpus\Normalizer\Tests\Functional\MockArticle instances.
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
        (self::$normalizer1)($ret, $object, $context, $normalizer);

        return $ret;
    }

    /**
     * Create and denormalize MakinaCorpus\Normalizer\Tests\Functional\MockArticle instances.
     *
     * @param callable $normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string $type PHP native type
     *      - mixed $input raw value from normalized data
     *      - Context $context the context
     */
    public static function denormalize(array $input, Context $context, ?callable $denormalizer = null): MockArticle
    {
        $ret = (new \ReflectionClass(MockArticle::class))->newInstanceWithoutConstructor();

        (self::$denormalizer0)($ret, $input, $context, $denormalizer);
        (self::$denormalizer1)($ret, $input, $context, $denormalizer);

        return $ret;
    }
}

/**
 * Normalizer for properties of MockArticle.
 */
MockArticleNormalizer::$normalizer0 = \Closure::bind(
    static function (array &$ret, MockArticle $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'id' property
        $ret['id'] = null === $object->id ? null : $object->id->__toString();

        // Normalize 'createdAt' property
        $ret['createdAt'] = null === $object->createdAt ? null : $normalizer ? $normalizer($object->createdAt, $context, $normalizer) : $object->createdAt;

        // Normalize 'updatedAt' property
        $ret['updatedAt'] = null === $object->updatedAt ? null : $normalizer ? $normalizer($object->updatedAt, $context, $normalizer) : $object->updatedAt;

        // Normalize 'authors' property
        $ret['authors'] = [];
        if ($object->authors) {
            foreach ($object->authors as $index => $value) {
                if (null === $value) {
                    $ret['authors'][$index] = null;
                } else {
                    $ret['authors'][$index] = (string)$value;
                }
            }
        }
    },
    null, MockArticle::class
);

/**
 * Denormalizer for properties of MockArticle.
 */
MockArticleNormalizer::$denormalizer0 = \Closure::bind(
    static function (MockArticle $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'id' required property
        if (!isset($input['id'])) {
            $context->addError(\sprintf("'%s' cannot be null", 'id'), $context);
        } else {
            $instance->id = \Ramsey\Uuid\Uuid::fromString($input['id']);
        }

        // Denormalize 'createdAt' required property
        if (!isset($input['createdAt'])) {
            $context->addError(\sprintf("'%s' cannot be null", 'createdAt'), $context);
        } else {
            $instance->createdAt = $denormalizer ? $denormalizer(\DateTimeInterface::class, $input['createdAt'], $context, $denormalizer) : $input['createdAt'];
        }

        // Denormalize 'updatedAt' required property
        if (!isset($input['updatedAt'])) {
            $context->addError(\sprintf("'%s' cannot be null", 'updatedAt'), $context);
        } else {
            $instance->updatedAt = $denormalizer ? $denormalizer(\DateTimeInterface::class, $input['updatedAt'], $context, $denormalizer) : $input['updatedAt'];
        }

        // Denormalize 'authors' collection property
        if (isset($input['authors'])) {
            if (!\is_iterable($input['authors'])) {
                $input['authors'] = (array)$input['authors'];
            }
            if ($input['authors']) {
                $instance->authors = [];
                foreach ($input['authors'] as $index => $value) {
                    if (null === $value) {
                        $context->addError("Property value in collection cannot be null");
                        $instance->authors[$index] = null;
                    } else {
                        $instance->authors[$index] = Helper::toString($value);
                    }
                }
            }
        }
    },
    null, MockArticle::class
);

/**
 * Normalizer for properties of MockWithText.
 */
MockArticleNormalizer::$normalizer1 = \Closure::bind(
    static function (array &$ret, MockWithText $object, Context $context, ?callable $normalizer = null): void {

        // Normalize 'title' property
        $ret['title'] = null === $object->title ? null : (string)$object->title;

        // Normalize 'text' property
        $ret['text'] = null === $object->text ? null : \Generated8\MakinaCorpus\Normalizer\Tests\Functional\MockTextWithFormatNormalizer::normalize($object->text, $context, $normalizer);
    },
    null, MockWithText::class
);

/**
 * Denormalizer for properties of MockWithText.
 */
MockArticleNormalizer::$denormalizer1 = \Closure::bind(
    static function (MockWithText $instance, array $input, Context $context, ?callable $denormalizer = null): void {

        // Denormalize 'title' nullable property
        $instance->title = isset($input['title']) ? Helper::toString($input['title']) : null;

        // Denormalize 'text' nullable property
        $instance->text = isset($input['text']) ? \Generated8\MakinaCorpus\Normalizer\Tests\Functional\MockTextWithFormatNormalizer::denormalize($input['text'], $context, $denormalizer) : null;
    },
    null, MockWithText::class
);
