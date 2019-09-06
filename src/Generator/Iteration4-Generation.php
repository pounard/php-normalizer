<?php
/**
 * Iteration #2.
 *
 * Generate code using helpers for iteration 3 and more direct type hydration
 * without going throught the (de)normalizer between types.
 */

declare(strict_types=1);

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\PropertyDefinition;

/**
 * Hydrate object using the generated hydrator
 */
function hydrator4(string $type, /* string|array|T */ $input, Context $context) /* : T */
{
    $external = hydrator1_external_implementation($type, $input, $context);
    if ($external->handled) {
        return $external->value;
    }

    $nativeType = $context->getNativeType($type);
    $normalizer = generate4_compute_normalizer_name($nativeType);

    return \call_user_func(
        [$normalizer, 'denormalize'],
        $input,
        $context,
        'hydrator4'
    );
}

/**
 * Compute normalizer name
 */
function generate4_compute_normalizer_name(string $nativeType): string
{
    $parts = \array_filter(\explode('\\', $nativeType));
    $localClassName = \array_pop($parts);
    return 'Generated4\\'.\implode('\\', $parts).'\\'.$localClassName.'Normalizer';
}

/**
 * Generate type validation condition
 */
function generate4_validation(PropertyDefinition $property, Context $context): string
{
    $nativeType = $context->getNativeType($property->getTypeName());

    if (!\class_exists($nativeType) && !interface_exists($nativeType)) {
        if (\strpos($nativeType, '\\')) {
            throw new \LogicException(\sprintf("Cannot dump normalizer: class '%s' for property '%s' does not exist", $nativeType, $property->getNativeName()));
        }

        if ($property->isOptional()) {
            return "null === \$value || \\MakinaCorpus\Normalizer\\gettype_real(\$value) === '".$nativeType."'";
        } else {
            return "\\MakinaCorpus\Normalizer\\gettype_real(\$value) === '".$nativeType."'";
        }
    } else if ($property->isOptional()) {
        return "null === \$value || \$value instanceof \\".$nativeType;
    } else {
        return "\$value instanceof \\".$nativeType;
    }
}

function generate4_property_handle_value(PropertyDefinition $property, Context $context, int $indent, string $variable): string
{
    $indentation = \str_repeat(" ", 4 * $indent);
    $propName = $property->getNativeName();
    $type = $context->getNativeType($property->getTypeName());
    $nativeType = \addslashes($type);
    $validation = generate4_validation($property, $context);
    $ret = [];

    // @todo Here allow custom implementation to write code
    // Scalar helpers validate at the same time.
    switch ($property->getTypeName()) {
        case 'bool':
            $ret[] = "{$variable} = Helper\\to_bool(\$value, \$context);";
            break;
        case 'float':
            $ret[] = "{$variable} = Helper\\to_float(\$value, \$context);";
            break;
        case 'int':
            $ret[] = "{$variable} = Helper\\to_int(\$value, \$context);";
            break;
        case 'null':
            break;
        case 'string':
            $ret[] = "{$variable} = Helper\\to_string(\$value, \$context);";
            break;
        default:
            $ret[] = "if (null !== \$value && \$normalizer) {";
            $ret[] = "    {$variable} = \$normalizer('{$nativeType}', \$value, \$context);";
            if ($property->isOptional()) {
                $ret[] = "    if (!(".$validation.")) {";
                $ret[] = "        Helper\\handle_error(\"Type mismatch\", \$context);";
                $ret[] = "        \$value = null;";
                $ret[] = "    }";
            } else {
                $ret[] = "    if (null === \$value) {";
                $ret[] = "        Helper\\handle_error(\"Property '{$propName}' cannot be null\", \$context);";
                $ret[] = "    } else if (!(".$validation.")) {";
                $ret[] = "        Helper\\handle_error(\"Type mismatch\", \$context);";
                $ret[] = "        \$value = null;";
                $ret[] = "    }";
            }
            $ret[] = "}";
            break;
    }

    return implode("\n".$indentation, $ret);
}

/**
 * Generate code for denormalizing a non-collection property
 */
function generate4_property_set(PropertyDefinition $property, Context $context, Writer $writer): void
{
    $propName = \addslashes($property->getNativeName());
    $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
    $handleCode = generate4_property_handle_value($property, $context, 2, '$value');

    $writer->write(<<<EOT
        // Denormalize '{$propName}' property
        \$value = Helper\\find_value(\$input, {$candidateNames}, \$context);
        {$handleCode}
        \call_user_func(self::\$accessor, \$ret, '{$propName}', \$value);
EOT
    );
}

/**
 * Generate code for denormalizing a collection property
 */
function generate4_property_set_collection(PropertyDefinition $property, Context $context, Writer $writer): void
{
    $propName = \addslashes($property->getNativeName());
    $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
    $handleCode1 = generate4_property_handle_value($property, $context, 3, '$values');
    $handleCode2 = generate4_property_handle_value($property, $context, 5, '$value');

    $writer->write(<<<EOT
        // Denormalize '{$propName}' collection property
        \$propValue = [];
        \$values = Helper\\find_value(\$input, {$candidateNames}, \$context);
        if (!\is_iterable(\$values)) {
            {$handleCode1}
            \$propValue[] = \$values;
        } else {
            foreach (\$values as \$index => \$value) {
                try {
                    \$context->enter((string)\$index);
                    {$handleCode2}
                    \$propValue[\$index] = \$value; 
                } finally {
                    \$context->leave();
                }
            }
        }
        \call_user_func(self::\$accessor, \$ret, '{$propName}', \$propValue);
EOT
    );
}

/**
 * Generate code for property denormalization
 */
function generate4_property_handle(PropertyDefinition $property, Context $context, Writer $writer): void
{
    if ($property->isCollection()) {
        generate4_property_set_collection($property, $context, $writer);
    } else {
        generate4_property_set($property, $context, $writer);
    }
}

/**
 * Generate denormalizer for a class
 */
function generate4_denormalizer_class(string $type, Context $context, Writer $writer): void
{
    $typeDef = $context->getType($type);
    $nativeType = $context->getNativeType($type);

    if ($typeDef->isTerminal()) {
        throw new \LogicException(\sprintf("Cannot dump normalizer: type '%s' is terminal", $nativeType));
    }
    if (!\class_exists($nativeType)) {
        throw new \LogicException(\sprintf("Cannot dump normalizer: type '%s' does not exist or is not a class", $nativeType));
    }

    // @todo this should be outed to a name strategy implementation
    $parts = \array_filter(\explode('\\', $nativeType));
    $localClassName = \array_pop($parts);
    $classNamespace = \implode('\\', $parts);

    $generatedClassName = \generate4_compute_normalizer_name($nativeType);
    $parts = \array_filter(\explode('\\', $generatedClassName));
    $generatedLocalClassName = \array_pop($parts);
    $generatedClassNamespace = \implode('\\', $parts);

    $imports = [Context::class];
    if ($generatedClassNamespace !== $classNamespace) {
        $imports[] = $nativeType;
    }
    \sort($imports);
    $importsAsString = "use ".\implode(";\nuse ", $imports).';';

    $writer->write(<<<EOT
<?php
/**
 * Generated (de)normalizer for class {$nativeType}.
 *
 * Do not modify it manually, re-generate it upon each code modification.
 */

declare(strict_types=1);

namespace {$generatedClassNamespace};

{$importsAsString}

use MakinaCorpus\Normalizer as Helper;

final class {$generatedLocalClassName}
{
    // @todo Use GeneratedHydrator trick for inheritance
    /** @var callable */
    public static \$accessor;

    /**
     * Create and normalize {$nativeType} instances.
     *
     * @param callable \$normalizer
     *   A callback that will hydrate externally handled values, parameters are:
     *      - string \$type PHP native type to hydrate
     *      - mixed \$input raw value from normalized data
     *      - Context \$context the context
     */
    public static function denormalize(array \$input, Context \$context, ?callable \$normalizer = null): {$localClassName}
    {
        \$ret = (new \ReflectionClass({$localClassName}::class))->newInstanceWithoutConstructor();
EOT
    );

    $properties = $typeDef->getProperties();
    if ($properties) {
        foreach ($typeDef->getProperties() as $property) {
            $writer->write("\n\n");
            generate4_property_handle($property, $context, $writer);
        }
    }

    $writer->write("\n\n");
    $writer->write(<<<EOT
        return \$ret;
    }
}

{$localClassName}Normalizer::\$accessor = \Closure::bind(
    static function ({$localClassName} \$instance, string \$propName, \$value): void {
        \$instance->{\$propName} = \$value;
    },
    null, {$localClassName}::class
);
EOT
    );

    $writer->write("\n");
}
