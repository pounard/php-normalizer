<?php
/**
 * Iteration #1.
 *
 * Pseudo code, reference implementation, mostly working but whose goal
 * is to provide a skeleton for object normalization and denormalization.
 *
 * Normalization considers that the PHP native object you give as input is
 * valid, there is no need to do validation.
 *
 * Denormalization considers all input values to be strings (or at least
 * scalar types), once values are hydrated they need to be validated to
 * ensure we don't mismatch PHP types in the hydrated object.
 *
 * Validation will never assume any business consideration, if need to do so
 * use a high level component atop such as symfony/validator once objects
 * have been hydrated.
 *
 * Validation will only validate type, collection type and mandatory status,
 * which are the only needed verification to ensure the lower level object
 * is correctly hydrated in a type safe manner.
 *
 * It always work on an array-based intermediate representation, which makes
 * everything easier for both ways translations.
 *
 * This code will be:
 *  - split into semanticly meaningful, single responsability, methods,
 *  - unit tested as much as possible,
 *  - converted into a event emitter definiction tree traversal in the end,
 *    upon which both validation and code generation can be plugged.
 */

declare(strict_types=1);

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\PropertyDefinition;
use function MakinaCorpus\Normalizer\gettype_real;
use Ramsey\Uuid\Uuid;

/**
 * Create instance and hydrate values
 */
function hydrator1_instance_new(string $type, Context $context)
{
    if (!\class_exists($type)) {
        $context->addError(\sprintf("Class '%s' does not exist", $type));
        return null;
    }
    return (new \ReflectionClass($type))->newInstanceWithoutConstructor();
}

/**
 * Create instance and hydrate values
 */
function hydrator1_instance_set_value(string $type, object $instance, string $propName, $value, Context $context): void
{
    $closure = \Closure::bind(static function (object $instance) use ($propName, $value) {
        $instance->{$propName} = $value;
    }, null, \get_class($instance));

    $closure($instance);
}

/**
 * Find raw value in tree
 */
function hydrator1_property_find(array $input, PropertyDefinition $property, Context $context) {
    $found = $value = null;

    foreach ($property->getCandidateNames() as $name) {
        if (\array_key_exists($name, $input)) {
            // This does not break on already found in order to populate
            // a complete error report.
            // @todo make this optional in context for speedup.
            //    - generic way: raise exceptions result instance
            if ($found) {
                $context->addError(\sprintf("Property '%s' found in '%s' but was already found in '%s'", $property->getNormalizedName(), $found, $name));
            } else {
                $found = $name;
                $value = $input[$name];
            }
        }
    }

    return $value;
}

/**
 * Extract a single value
 */
function hydrator1_property_extract(array $input, PropertyDefinition $property, Context $context)
{
    $value = hydrator1_property_find($input, $property, $context);
    if (null !== $value) {
        return hydrator1($property->getTypeName(), $value, $context);
    }
    return null;
}

/**
 * Validate value
 */
function hydrator1_property_validate($value, PropertyDefinition $property, Context $context)
{
    if (null === $value) {
        if (!$property->isOptional()) {
            $context->addError("Property cannot be null");
        }
        return $value;
    }

    $type = gettype_real($value);
    $expected = $context->getNativeType($property->getTypeName());

    $isValid = false;
    if (class_exists($expected) || interface_exists($expected)) {
        $isValid = ($value instanceof $expected);
    } else {
        $isValid = ($type === $expected);
    }

    if (!$isValid) {
        $context->addError(\sprintf("Property type mismatch: expected '%s' got '%s'", $expected, $type));
    }

    return $value;
}

/**
 * Validate value collection
 */
function hydrator1_property_validate_collection(iterable $values, PropertyDefinition $property, Context $context): iterable
{
    foreach ($values as $index => $value) {
        try {
            $context->enter((string)$index);
            yield $index => hydrator1_property_validate($value, $property, $context);
        } finally {
            $context->leave();
        }
    }
}

/**
 * Extract a single value
 */
function hydrator1_property_extract_collection(array $input, PropertyDefinition $property, Context $context)
{
    $values = hydrator1_property_find($input, $property, $context);

    if (\is_iterable($values)) {
        foreach ($values as $index => $value) {
            if (null === $value) {
                yield $index => null;
            } else {
                yield $index => hydrator1($property->getTypeName(), $value, $context);
            }
        }
    } else {
        yield $values;
    }
}

/**
 * Handle single value
 */
function hydrator1_property_handle(array $input, PropertyDefinition $property, Context $context)
{
    try {
        $context->enter($property->getNativeName());
        if (!$property->isCollection()) {
            return hydrator1_property_validate(
                hydrator1_property_extract($input, $property, $context),
                $property,
                $context
            );
        }
        return \iterator_to_array(
            hydrator1_property_validate_collection(
                hydrator1_property_extract_collection($input, $property, $context),
                $property, $context
            )
        );
    } finally {
        $context->leave($property->getNormalizedName());
    }
}

/**
 * Option type for hydrator1_external_implementation()
 */
final class HydratorOption
{
    public $handled;
    public $value;

    /**
     * Got a value
     */
    public static function ok($value)
    {
        $ret = new self;
        $ret->value = $value;
        $ret->handled = true;
        return $ret;
    }

    /**
     * Does not handle type
     */
    public static function miss()
    {
        return new self;
    }
}

/**
 * Degradation of re-entring into the generator
 *
 * @todo This is basically cheating in benchmarks.
 */
function hydrator1_external_implementation(string $type, $input, Context $context): HydratorOption {
    switch ($type) {
        case 'bool':
            return HydratorOption::ok($input);
        case 'float':
            return HydratorOption::ok($input);
        case 'int':
            return HydratorOption::ok($input);
        case 'null':
            return HydratorOption::ok($input);
        case 'string':
            return HydratorOption::ok($input);
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
function hydrator1(string $type, /* string|array|T */ $input, Context $context) /* : T */
{
    $nativeType = $context->getNativeType($type);

    $external = hydrator1_external_implementation($nativeType, $input, $context);
    if ($external->handled) {
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

    $instance = hydrator1_instance_new($typeDef->getNativeName(), $context);

    if (null === $instance) {
        return $instance;
    }

    /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
    foreach ($typeDef->getProperties() as $property) {
        hydrator1_instance_set_value(
            $nativeType, $instance, $property->getNativeName(),
            hydrator1_property_handle($input, $property, $context),
            $context
        );
    }

    return $instance;
}
