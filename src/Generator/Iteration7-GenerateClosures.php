<?php
/**
 * Iteration #7.
 *
 * Re-use code from iteration #7 - generate code within closures for direct
 * objects property access, and correct property access, depending on the
 * declaring class.
 */

declare(strict_types=1);

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;

/**
 * Generator wrapper, with naming strategy
 */
final class Generator7Impl implements Generator5
{
    /** @var \MakinaCorpus\Normalizer\ContextFactory */
    private $contextFactory;

    /** @var \MakinaCorpus\Normalizer\NamingStrategy */
    private $namingStrategy;

    /** @var string */
    private $projectPsr4Namespace;

    /** @var string */
    private $projectSourceDirectory;

    /**
     * Constructor
     *
     * @param string $projectSourceRoot
     */
    public function __construct(ContextFactory $contextFactory, string $projectSourceDirectory, ?string $projectPsr4Namespace = null)
    {
        $this->contextFactory = $contextFactory;
        $this->namingStrategy = new Psr4AppNamingStrategy('Normalizer', 'Generated7');
        $this->projectPsr4Namespace = $projectPsr4Namespace;
        $this->projectSourceDirectory = $projectSourceDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizerClass(string $className): string
    {
        return $this->namingStrategy->generateClassName($className, '\\');
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizerClass(string $className): string
    {
        $normalizerClassName = $this->namingStrategy->generateClassName($className, '\\');
        $filename = $this->namingStrategy->generateFilename($className, $this->projectSourceDirectory);
        $directory = \dirname($filename);

        if (!\is_dir($directory) && !@\mkdir($directory, 0750, true)) {
            throw new \RuntimeException(\sprintf("%s: could not create directory", $directory));
        }
        if (!\is_writable($directory)) {
            throw new \RuntimeException(\sprintf("%s: directory is not writable", $directory));
        }

        $writer = new \Writer($filename);
        $context = $this->contextFactory->createContext();

        return $this->generateClass($className, $normalizerClassName, $context, $writer);
    }

    /**
     * Generate type validation condition
     */
    private function generatePropertyValueValidate(PropertyDefinition $property, Context $context): string
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
     * Generate value conversion code
     */
    private function generatePropertyValueConvert(PropertyDefinition $property, Context $context, int $indent, string $variable): string
    {
        $indentation = \str_repeat(" ", 4 * $indent);
        $propName = $property->getNativeName();
        $type = $context->getNativeType($property->getTypeName());
        $nativeType = \addslashes($type);
        $validation = $this->generatePropertyValueValidate($property, $context);
        $ret = [];

        // Attempt eager related class code generation, if possible.
        try {
            if (\class_exists($type)) {
                $normalizerClassName = $this->generateNormalizerClass($type);
                $ret[] = "if (null !== \$value) {";
                $ret[] = "    \$value = \\".$normalizerClassName."::denormalize(\$value, \$context, \$normalizer);";
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

                return \implode("\n".$indentation, $ret);
            }
        } catch (TypeDoesNotExistError $e) {
            $context->addWarning($e->getMessage());
        }

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

        return \implode("\n".$indentation, $ret);
    }

    /**
     * Generate single value property set
     */
    private function generatePropertyValue(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
        $handleCode = $this->generatePropertyValueConvert($property, $context, 2, '$value');

        $writer->write(<<<EOT
        // Denormalize '{$propName}' property
        \$value = Helper\\find_value(\$input, {$candidateNames}, \$context);
        {$handleCode}
        \$instance->{$propName} = \$value;
EOT
        );
    }

    /**
     * Generate value collection property set
     */
    private function generatePropertyCollection(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
        $handleCode1 = $this->generatePropertyValueConvert($property, $context, 3, '$values');
        $handleCode2 = $this->generatePropertyValueConvert($property, $context, 5, '$value');

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
        \$instance->{$propName} = \$propValue;
EOT
        );
    }

    /**
     * Generate property handling code
     */
    private function generateProperty(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        if ($property->isCollection()) {
            $this->generatePropertyCollection($property, $context, $writer);
        } else {
            $this->generatePropertyValue($property, $context, $writer);
        }
    }

    /**
     * Generate denormalizer for a class
     *
     * @return string
     *   The generated class fully qualified name
     */
    private function generateClass(string $type, string $generatedClassName, Context $context, Writer $writer): string
    {
        $typeDef = $context->getType($type);
        $nativeType = $context->getNativeType($type);

        if ($typeDef->isTerminal()) {
            throw new \LogicException(\sprintf("Cannot dump normalizer: type '%s' is terminal", $nativeType));
        }
        if (!\class_exists($nativeType)) {
            throw new \LogicException(\sprintf("Cannot dump normalizer: type '%s' does not exist or is not a class", $nativeType));
        }

        $parts = \array_filter(\explode('\\', $nativeType));
        $localClassName = \array_pop($parts);
        $classNamespace = \implode('\\', $parts);

        $parts = \array_filter(\explode('\\', $generatedClassName));
        $generatedLocalClassName = \array_pop($parts);
        $generatedClassNamespace = \implode('\\', $parts);

        $imports = [Context::class, 'MakinaCorpus\Normalizer as Helper'];
        if ($generatedClassNamespace !== $classNamespace) {
            $imports[] = $nativeType;
        }

        // Class static closures.
        $closures = [];

        // Create a property map based upon the declaring class and access
        // scope (private, protected).
        $perClassMap = [];
        $index = 0;
        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
        foreach ($properties = $typeDef->getProperties() as $property) {
            // Get relative name.
            // @todo handle name conflicts.
            $parts = \array_filter(\explode('\\', $declaringClass = $property->getDeclaringClass()));
            $localDeclaringClassName = \array_pop($parts);
            // Only create a new closure for when the property cannot be
            // accessed by the hydrated class.
            if ('private' === $property->getDeclaredScope() && $declaringClass !== $nativeType) {
                $imports[] = $declaringClass;
                $perClassMap[$localDeclaringClassName][] = $property;
            } else {
                $perClassMap[$localDeclaringClassName][] = $property;
            }
            $closures[$localClassName] = 'denormalizer'.($index++);
        }

        // Generate imports.
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

/**
 * Public implementation of (de)normalizer for class {$localClassName}.
 */
final class {$generatedLocalClassName}
{
EOT
        );

        $writer->write("\n");
        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write(<<<EOT
    /** @var callable */
    public static \$denormalizer{$index};
EOT
            );
            $writer->write("\n\n");
        }

        $writer->write(<<<EOT
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

        $writer->write("\n");
        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write("\n");
            $writer->write(<<<EOT
        (self::\$denormalizer{$index})(\$ret, \$input, \$context, \$normalizer);
EOT
            );
        }
        $writer->write("\n\n");

        $writer->write(<<<EOT
        return \$ret;
    }
}
EOT
        );

        $index = 0;
        foreach ($perClassMap as $className => $properties) {
            $writer->write("\n\n");
            $writer->write(<<<EOT
/**
 * Denormalizer for properties of {$className}.
 */
{$localClassName}Normalizer::\$denormalizer{$index} = \Closure::bind(
    static function ({$className} \$instance, array \$input, Context \$context, ?callable \$normalizer = null): void {
EOT
            );

            foreach ($properties as $property) {
                $writer->write("\n\n");
                $this->generateProperty($property, $context, $writer);
            }

            $writer->write("\n");
            $writer->write(<<<EOT
    },
    null, {$className}::class
);
EOT
            );
            $index++;
        }

        $writer->write("\n");

        return $generatedClassNamespace.'\\'.$generatedLocalClassName;
    }
}
