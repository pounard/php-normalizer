<?php
/**
 * Iteration #2.
 *
 * Generate code.
 */

declare(strict_types=1);

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\PropertyDefinition;

/**
 * Simple test write
 */
final class Writer
{
    /** @var resource */
    private $handle;

    /**
     * Constructor
     */
    public function __construct(string $filename)
    {
        if (\file_exists($filename)) {
            if (!@\unlink($filename)) {
                throw new \RuntimeException(\sprintf("'%s': can not delete file"));
            }
        }
        if (false === ($this->handle = \fopen($filename, "a+"))) {
            throw new \RuntimeException(\sprintf("'%s': can not open file for writing"));
        }
    }

    /**
     * Append text to generated file
     */
    public function write(string $string): void
    {
        if (!$this->handle) {
            throw new \RuntimeException("File was closed");
        }
        \fwrite($this->handle, $string);
    }

    /**
     * Close file
     */
    public function close(): void
    {
        if ($this->handle) {
            @fclose($this->handle);
        }
        $this->handle = null;
    }
}

/**
 * Hydrate object using the generated hydrator
 */
function hydrator2(string $type, /* string|array|T */ $input, Context $context) /* : T */
{
    $external = hydrator1_external_implementation($type, $input, $context);
    if ($external->handled) {
        return $external->value;
    }

    $nativeType = $context->getNativeType($type);
    $normalizer = generate2_compute_normalizer_name($nativeType);

    return \call_user_func(
        [$normalizer, 'denormalize'],
        $input,
        $context,
        'hydrator2'
    );
}

/**
 * Compute normalizer name
 */
function generate2_compute_normalizer_name(string $nativeType): string
{
    $parts = \array_filter(\explode('\\', $nativeType));
    $localClassName = \array_pop($parts);
    return 'Generated2\\'.\implode('\\', $parts).'\\'.$localClassName.'Normalizer';
}

/**
 * Generate type validation condition
 */
function generate2_validation(PropertyDefinition $property, Context $context): string
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

/**
 * Generate code for denormalizing a non-collection property
 */
function generate2_property_set(PropertyDefinition $property, Context $context, Writer $writer)
{
    $propName = \addslashes($property->getNativeName());
    $type = $context->getNativeType($property->getTypeName());
    $nativeType = \addslashes($type);
    $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
    $validation = generate2_validation($property, $context);

    $writer->write(<<<EOT
        // Denormalize '{$propName}' property
        \$value = self::find('{$propName}', \$input, {$candidateNames}, \$context);
EOT
    );

    if (!$property->isOptional()) {
        $writer->write("\n");
        $writer->write(<<<EOT
        if (null === \$value) {
            \$context->addError("Property '{$propName}' cannot be null");
        }
EOT
        );
    }

    $writer->write("\n");
    $writer->write(<<<EOT
        if (null !== \$value && \$normalizer) {
            \$value = \$normalizer('{$nativeType}', \$value, \$context);
        }
        if (!{$validation}) {
            \$value = null;
        }
        \call_user_func(self::\$accessor, \$ret, '{$propName}', \$value);
EOT
    );
}

/**
 * Generate code for denormalizing a collection property
 */
function generate2_property_set_collection(PropertyDefinition $property, Context $context, Writer $writer)
{
    $propName = \addslashes($property->getNativeName());
    $type = $context->getNativeType($property->getTypeName());
    $nativeType = \addslashes($type);
    $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
    $validation = generate2_validation($property, $context);

    $writer->write(<<<EOT
        // Denormalize '{$propName}' collection property
        \$values = self::find('{$propName}', \$input, {$candidateNames}, \$context);
        if (null === \$values) {
            \$propValue = [];
        } else {
            if (!\is_iterable(\$values)) {
                \$values = [\$values];
            }
            \$propValue = [];
            foreach (\$values as \$index => \$value) {
                if (null !== \$value && \$normalizer) {
                    \$value = \$normalizer('{$nativeType}', \$value, \$context);
                }
                if ({$validation}) {
                    \$propValue[\$index] = \$value;
                } else {
                    \$propValue[\$index] = null;
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
function generate2_property_handle(PropertyDefinition $property, Context $context, Writer $writer)
{
    if ($property->isCollection()) {
        generate2_property_set_collection($property, $context, $writer);
    } else {
        generate2_property_set($property, $context, $writer);
    }
}

/**
 * Generate denormalizer for a class
 */
function generate2_denormalizer_class(string $type, Context $context, Writer $writer)
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

    $generatedClassName = \generate2_compute_normalizer_name($nativeType);
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
            generate2_property_handle($property, $context, $writer);
        }
    }

    $writer->write("\n\n");
    $writer->write(<<<EOT
        return \$ret;
    }

    /**
     * Find value matching in array
     */
    private static function find(string \$propName, array \$input, array \$names, Context \$context)
    {
        \$found = \$value = null;
        foreach (\$names as \$name) {
            if (\array_key_exists(\$name, \$input)) {
                if (\$found) {
                    \$context->addError(\sprintf("Property '%s' found in '%s' but was already found in '%s'", \$propName, \$found, \$name));
                } else {
                    \$found = \$name;
                    \$value = \$input[\$name];
                }
            }
        }
        return \$value;
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
