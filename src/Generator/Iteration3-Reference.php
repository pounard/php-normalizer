<?php
/**
 * Iteration #3.
 *
 * Rework of iteration 1, using helpers for scalar types.
 */

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\PropertyDefinition;
use function MakinaCorpus\Normalizer\find_value;
use function MakinaCorpus\Normalizer\to_bool;
use function MakinaCorpus\Normalizer\to_float;
use function MakinaCorpus\Normalizer\to_int;
use function MakinaCorpus\Normalizer\to_string;
use Ramsey\Uuid\Uuid;

/**
 * Create instance and hydrate values
 */
function hydrator3_instance_new(string $type, Context $context)
{
    if ('stdClass' === $type) {
        return new \stdClass();
    }
    if (!\class_exists($type)) {
        if (interface_exists($type)) {
            $context->addError(\sprintf("Cannot create an instance of interface '%s'", $type));
            return null;
        }
        $context->addError(\sprintf("Class '%s' does not exist", $type));
        return null;
    }
    return (new \ReflectionClass($type))->newInstanceWithoutConstructor();
}

/**
 * Create instance and hydrate values
 */
function hydrator3_instance_set_value(string $type, object $instance, string $propName, $value, Context $context): void
{
    $closure = \Closure::bind(static function (object $instance) use ($propName, $value) {
        $instance->{$propName} = $value;
    }, null, \get_class($instance));

    $closure($instance);
}

/**
 * Validate value
 */
function hydrator3_property_validate($value, PropertyDefinition $property, Context $context)
{
    if (null === $value) {
        if (!$property->isOptional()) {
            $context->addError("Property cannot be null");
        }
        return $value;
    }

    $type = \is_object($value) ? \get_class($value) : \gettype($value);

    if ($type !== ($expected = $property->getTypeName())) {
        $context->addError(\sprintf("Property type mismatch: expected '%s' got '%s'", $expected, $type));
    }

    return $value;
}

/**
 * Extract a single value
 */
function hydrator3_property_handle_value($value, PropertyDefinition $property, Context $context)
{
    if (!hydrator3_property_validate($value, $property, $context)) {
        return null;
    }
    if (null !== $value) {
        return hydrator3($property->getTypeName(), $value, $context);
    }
    return null;
}

/**
 * Extract a single value
 */
function hydrator3_property_handle_collection($values, PropertyDefinition $property, Context $context)
{
    if (!\is_iterable($values)) {
        return [hydrator3_property_handle_value($values, $property, $context)];
    }

    $ret = [];
    foreach ($values as $index => $value) {
        try {
            $context->enter((string)$index);
            $ret[$index] = hydrator3_property_handle_value($value, $property, $context);
        } finally {
            $context->leave();
        }
    }

    return $ret;
}

/**
 * Handle single value
 */
function hydrator3_property_handle(array $input, PropertyDefinition $property, Context $context)
{
    try {
        $context->enter($property->getNativeName());
        $value = find_value($input, $property->getCandidateNames(), $context);
        if ($property->isCollection()) {
            return hydrator3_property_handle_collection($value, $property, $context);
        } else {
            return hydrator3_property_handle_value($value, $property, $context);
        }
    } finally {
        $context->leave($property->getNormalizedName());
    }
}

/**
 * Degradation of re-entring into the generator
 *
 * @todo This is basically cheating in benchmarks.
 */
function hydrator3_external_implementation(string $type, $input, Context $context): HydratorOption
{
    switch ($type) {
        case 'bool':
            return HydratorOption::ok(to_bool($input, $context));
        case 'float':
            return HydratorOption::ok(to_float($input, $context));
        case 'int':
            return HydratorOption::ok(to_int($input, $context));
        case 'null':
            return HydratorOption::ok($input);
        case 'string':
            return HydratorOption::ok(to_string($input, $context));
        // Those must be externalised.
        case 'date':
        case 'DateTime':
        case 'DateTimeInterface':
        case 'DateTimeImmutable':
            return HydratorOption::ok(new \DateTimeImmutable($input));
        case Ramsey\Uuid\UuidInterface::class:
            return HydratorOption::ok(Uuid::fromString($input));
    }
    return HydratorOption::miss();
}

/**
 * Hydrate object
 */
function hydrator3(string $type, /* string|array|T */ $input, Context $context) /* : T */
{
    $external = hydrator3_external_implementation($type, $input, $context);
    if ($external->handled) {
        return $external->value;
    }

    $typeDef = $context->getType($type);

    if ($typeDef->isTerminal()) {
        // Custom normalizer
        $context->addWarning("Definition is terminal, custom processing is not implemented yet");

        return $input;
    }

    if (!\is_array($input)) {
        $context->addError("Definition is not terminal, input is not an array");

        return null;
    }

    $instance = hydrator3_instance_new($typeDef->getNativeName(), $context);

    if (null === $instance) {
        return $instance;
    }

    /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
    foreach ($typeDef->getProperties() as $property) {
        hydrator3_instance_set_value(
            $type, $instance, $property->getNativeName(),
            hydrator3_property_handle($input, $property, $context),
            $context
        );
    }

    return $instance;
}
