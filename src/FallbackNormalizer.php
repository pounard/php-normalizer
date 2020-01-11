<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Object (de)normalizer to use whenever the generated variant does not exist.
 *
 * This is the reference implementation, it recursively traverse the object or
 * input hierarchy, following the type and property definition, and handle
 * whatever matches those definitions.
 *
 * As being the reference implementation, it's also the "most readable" one
 * which covers 100% of unit tests. To write a new one, understand this one
 * first.
 *
 * Definitions are handled by the context.
 */
final class FallbackNormalizer
{
    /**
     * Create a normalizer instance using this implementation.
     */
    public static function create(): Normalizer
    {
        return new class () implements Normalizer
        {
            public function normalize($object, Context $context)
            {
                return FallbackNormalizer::normalize($object, $context);
            }

            public function denormalize(string $type, $input, Context $context)
            {
                return FallbackNormalizer::denormalize($type, $input, $context);
            }
        };
    }

    /**
     * Create object empty instance
     */
    private static function objectCreate(string $type, Context $context)
    {
        if (!\class_exists($type)) {
            $context->classDoesNotExistError($type);
            return null;
        }
        return (new \ReflectionClass($type))->newInstanceWithoutConstructor();
    }

    /**
     * Create instance and hydrate values
     */
    private static function objectValueSet($object, $value, PropertyDefinition $property): void
    {
        $stealer = \Closure::bind(
            static function () use ($object, $value, $property) {
                $object->{$property->getNativeName()} = $value;
            },
            null, $property->getDeclaringClass()
        );
        $stealer();
    }

    /**
     * Extract property value from object
     */
    private static function objectValueGet($object, PropertyDefinition $property)
    {
        $stealer = \Closure::bind(
            static function () use ($object, $property) {
                return $object->{$property->getNativeName()};
            },
            null, $property->getDeclaringClass()
        );
        return $stealer();
    }

    /**
     * Denormalize value as a collection, null input here is not allowed.
     */
    private static function valueDenormalizeCollection($values, PropertyDefinition $property, Context $context): array
    {
        $ret = [];
        $type = $property->getTypeName();

        if (!\is_iterable($values)) {
            $values = [$values];
        }

        foreach ($values as $index => $value) {
            if (null === $value) {
                $context->nullValueError($type);
                $ret[$index] = null; // Let it pass if partial allowed.
            } else {
                $ret[$index] = self::denormalize($type, $value, $context);
            }
        }

        return $ret;
    }

    /**
     * Handle single value
     */
    private static function propertyDenormalize(array $input, PropertyDefinition $property, Context $context): ValueOption
    {
        $propName = $property->getNativeName();
        $isCollection = $property->isCollection();

        try {
            $context->enter($propName);

            $option = RuntimeHelper::find($input, $property->getCandidateNames(), $context);

            // No value in incomming array, check for disallowed null
            // values and pass denormalization.
            if (!$option->success) {
                if (!$isCollection && !$property->isOptional()) {
                    $context->nullValueError($property->getTypeName());
                }
                return $option;
            }

            if ($isCollection) {
                return ValueOption::ok(
                    self::valueDenormalizeCollection(
                        $option->value, $property, $context
                    )
                );
            }

            // Shortcuts null values, which don't need denormalization.
            if (null === $option->value) {
                if (!$property->isOptional()) {
                    $context->nullValueError($property->getTypeName());
                }
                return $option;
            }

            return ValueOption::ok(
                self::denormalize(
                    $property->getTypeName(), $option->value, $context
                )
            );
        } finally {
            $context->leave($propName);
        }
    }

    /**
     * Hydrate object
     */
    public static function denormalize(string $type, $input, Context $context)
    {
        $nativeType = $context->getNativeType($type);

        if ($input instanceof $nativeType) {
            // Value is already denormalized, I guess it should not happen,
            // but still it seems legit to do that.
            return $input;
        }

        $external = RuntimeHelper::denormalizeScalar($nativeType, $input, $context);
        if ($external->success) {
            return $external->value;
        }

        $typeDef = $context->getType($nativeType);

        if ($typeDef->isTerminal()) {
            // Custom normalizer
            $context->addWarning("Definition is terminal, custom processing is not implemented yet");

            return $input;
        }

        if (!\is_array($input)) {
            $context->addError("Definition is not terminal, input is not an array");

            return null;
        }

        $instance = self::objectCreate($nativeType, $context);

        if (null === $instance) {
            return $instance;
        }

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
        foreach ($typeDef->getProperties() as $property) {
            $option = self::propertyDenormalize($input, $property, $context);
            if ($option->success) {
                self::objectValueSet($instance, $option->value, $property);
            }
        }

        return $instance;
    }

    /**
     * Handle property
     */
    private static function normalizeProperty($object, PropertyDefinition $property, Context $context)
    {
        try {
            $context->enter($property->getNativeName());

            $type = $property->getTypeName();
            $values = self::objectValueGet($object, $property);

            if (!$property->isCollection()) {
                return self::doNormalize($type, $values, $context);
            }

            // Check for emptyness, we don't support non nullable collections.
            if (null === $values || [] === $values) {
                return [];
            }

            // If collection was wrongly NOT a collection.
            if (!\is_iterable($values)) {
                try {
                    $context->enter("0");
                    return [self::doNormalize($type, $values, $context)];
                } finally {
                    $context->leave();
                }
            }

            // Normal collection processing.
            $ret = [];
            foreach ($values as $index => $value) {
                try {
                    $context->enter((string)$index);
                    $ret[$index] = self::doNormalize($type, $value, $context);
                } finally {
                    $context->leave();
                }
            }
            return $ret;

        } finally {
            $context->leave($property->getNormalizedName());
        }
    }

    /**
     * Normalize single value
     */
    private static function doNormalize(string $type, $object, Context $context)
    {
        // On normalize, we trust the incomming object, and allow null values.
        if (null === $object) {
            return null;
        }

        $option = RuntimeHelper::normalizeScalar($type, $object, $context);
        if ($option->success) {
            return $option->value;
        }

        $typeDef = $context->getType($type);

        if ($typeDef->isTerminal()) {
            $context->addWarning("Definition is terminal, custom processing is not implemented yet");

            return $object;
        }

        $ret = [];

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
        foreach ($typeDef->getProperties() as $property) {
            $ret[$property->getNormalizedName()] = self::normalizeProperty($object, $property, $context);
        }

        return $ret;
    }

    /**
     * Hydrate object
     */
    public static function normalize($object, Context $context)
    {
        return self::doNormalize(RuntimeHelper::getType($object), $object, $context);
    }
}
