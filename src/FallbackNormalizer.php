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
     * Create instance and hydrate values
     */
    private static function createInstance(string $type, Context $context)
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
    private static function propertySet($object, $value, PropertyDefinition $property, Context $context): void
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
     * Extract a single value
     */
    private static function propertyExtract(array $input, PropertyDefinition $property, Context $context)
    {
        if (1 === \count($candidateNames = $property->getCandidateNames())) {
            return $input[\reset($candidateNames)] ?? null;
        }

        $option = Helper::find($input, $candidateNames, $context);
        if ($option->success) {
            return $option->value;
        }
    }

    /**
     * Validate value
     */
    private static function propertyValidate($value, PropertyDefinition $property, Context $context)
    {
        $propType = $property->getTypeName();

        if (null === $value) {
            if (!$property->isOptional()) {
                $context->nullValueError($propType);
            }
            return $value; // Shortcut.
        }

        if ('null' === ($expected = $context->getNativeType($propType))) { // Shortcut.
            return $value;
        }

        if (($type = Helper::getType($value)) !== $expected && !$value instanceof $expected) {
            $context->typeMismatchError($expected, $type);
        }

        return $value;
    }

    /**
     * Extract a single value
     */
    private static function propertyDenormalizeCollection(array $input, PropertyDefinition $property, Context $context)
    {
        $ret = [];
        $values = self::propertyExtract($input, $property, $context);
        $type = $property->getTypeName();

        if (\is_iterable($values)) {
            foreach ($values as $index => $value) {
                if (null === $value) {
                    $context->nullValueError($type);
                    // Let it pass if partial allowed.
                    $ret[$index] = null;
                } else {
                    // $value, );
                    $ret[$index] = self::propertyValidate(
                        self::denormalize($type, $value, $context),
                        $property, $context
                    );
                }
            }
        } else {
            // Wronly not an iterable object, must be a single value.
            $ret[] = self::propertyValidate(
                self::denormalize($type, $values, $context),
                $property, $context
            );
        }

        return $ret;
    }

    /**
     * Handle single value
     */
    private static function denormalizeProperty(array $input, PropertyDefinition $property, Context $context)
    {
        $propName = $property->getNativeName();
        try {
            $context->enter($propName);

            if ($property->isCollection()) {
                return self::propertyDenormalizeCollection($input, $property, $context);
            }

            $value = self::propertyExtract($input, $property, $context);

            if (null === $value) {
                if (!$property->isOptional()) {
                    $context->nullValueError($property->getTypeName());
                }
                return null; // Fallback to null in case partial is allowed.
            }

            return self::propertyValidate(
                self::denormalize(
                    $property->getTypeName(), $value, $context
                ),
                $property, $context
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

        $external = Helper::denormalizeScalar($nativeType, $input, $context);
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

        $instance = self::createInstance($typeDef->getNativeName(), $context);

        if (null === $instance) {
            return $instance;
        }

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
        foreach ($typeDef->getProperties() as $property) {
            self::propertySet(
                $instance,
                self::denormalizeProperty($input, $property, $context),
                $property,
                $context
            );
        }

        return $instance;
    }

    /**
     * Extract property value from object
     */
    private static function propertyGet($object, PropertyDefinition $property, Context $context)
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
     * Handle property
     */
    private static function normalizeProperty($object, PropertyDefinition $property, Context $context)
    {
        try {
            $context->enter($property->getNativeName());

            $type = $property->getTypeName();
            $values = self::propertyGet($object, $property, $context);

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

        $option = Helper::normalizeScalar($type, $object, $context);
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
        return self::doNormalize(Helper::getType($object), $object, $context);
    }
}
