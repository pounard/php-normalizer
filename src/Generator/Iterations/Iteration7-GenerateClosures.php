<?php
/**
 * Iteration #7.
 *
 * Re-use code from iteration #7 - generate code within closures for direct
 * objects property access, and correct property access, depending on the
 * declaring class.
 *
 * Next step:
 *   - better error handling, add handle_type_mismatch() function,
 *   - remove enter() and leave() calls in generated code,
 *   - then normalizers!
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Iterations;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;
use MakinaCorpus\Normalizer\Generator\Generator;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Generator\Writer;

/**
 * Generator wrapper, with naming strategy
 */
final class Generator7Impl implements Generator
{
    /** @var ContextFactory */
    private $contextFactory;

    /** @var \MakinaCorpus\Normalizer\Generator\NamingStrategy */
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

        $writer = new Writer($filename);
        $context = $this->contextFactory->createContext();

        return $this->generateClass($className, $normalizerClassName, $context, $writer);
    }

    /**
     * Generate value conversion code
     */
    private function generateNormalizerPropertyValueConvert(PropertyDefinition $property, Context $context, int $indent, string $variable): string
    {
        $indentation = \str_repeat(" ", 4 * $indent);
        $propName = $property->getNativeName();
        $escapedPropName = \addslashes($propName);
        $type = $context->getNativeType($property->getTypeName());
        $ret = [];

        // Temporary variable, we may get rid of this one, but I'm not sure
        // this will ever make any differences in speed.
        $ret[] = "\$value = \$object->".$propName.";";

        // Attempt eager related class code generation, if possible.
        try {
            if (\class_exists($type)) {
                $normalizerClassName = $this->generateNormalizerClass($type);
                $ret[] = "if (null !== \$value) {";
                $ret[] = "    \$value = \\".$normalizerClassName."::normalize(\$value, \$context, \$normalizer);";
                if (!$property->isOptional()) {
                    $ret[] = "    if (null === \$value) {";
                    $ret[] = "        Helper\\handle_error(\"Property '{$escapedPropName}' cannot be null\", \$context);";
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
                $ret[] = "    {$variable} = \$normalizer(\$value, \$context);";
                if (!$property->isOptional()) {
                    $ret[] = "    if (null === \$value) {";
                    $ret[] = "        Helper\\handle_error(\"Property '{$escapedPropName}' cannot be null\", \$context);";
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
    private function generateNormalizerPropertyValue(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $normalizedName = \addslashes($property->getNormalizedName());
        $handleCode = $this->generateNormalizerPropertyValueConvert($property, $context, 2, '$value');

        $writer->write(<<<EOT
        // Denormalize '{$propName}' property
        {$handleCode}
        \$ret['{$normalizedName}'] = \$value;
EOT
        );
    }

    /**
     * Generate value collection property set
     */
    private function generateNormalizerPropertyCollection(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $normalizedName = \addslashes($property->getNormalizedName());
        $handleCode1 = $this->generateDenormalizerPropertyValueConvert($property, $context, 3, '$values');
        $handleCode2 = $this->generateDenormalizerPropertyValueConvert($property, $context, 5, '$value');

        $writer->write(<<<EOT
        // Denormalize '{$propName}' collection property
        \$normalizedValues = [];
        \$values = \$object->{$propName};
        if (!\is_iterable(\$values)) {
            {$handleCode1}
            \$normalizedValues[] = \$values;
        } else {
            foreach (\$values as \$index => \$value) {
                {$handleCode2}
                \$normalizedValues[\$index] = \$value;
            }
        }
        \$ret['{$normalizedName}'] = \$normalizedValues;
EOT
        );
    }

    /**
     * Generate property handling code
     */
    private function generateNormalizerProperty(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        if ($property->isCollection()) {
            $this->generateNormalizerPropertyCollection($property, $context, $writer);
        } else {
            $this->generateNormalizerPropertyValue($property, $context, $writer);
        }
    }

    /**
     * Generate type validation condition
     */
    private function generateDenormalizerPropertyValueValidate(PropertyDefinition $property, Context $context): string
    {
        $nativeType = $context->getNativeType($property->getTypeName());

        if (!\class_exists($nativeType) && !interface_exists($nativeType)) {
            if (\strpos($nativeType, '\\')) {
                throw new \LogicException(\sprintf("Cannot dump normalizer: class '%s' for property '%s' does not exist", $nativeType, $property->getNativeName()));
            }

            if ($property->isOptional()) {
                return "null === \$value || \\MakinaCorpus\Normalizer\\Helper::getType(\$value) === '".$nativeType."'";
            } else {
                return "\\MakinaCorpus\Normalizer\\Helper::getType(\$value) === '".$nativeType."'";
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
    private function generateDenormalizerPropertyValueConvert(PropertyDefinition $property, Context $context, int $indent, string $variable): string
    {
        $indentation = \str_repeat(" ", 4 * $indent);
        $propName = $property->getNativeName();
        $escapedPropName = \addslashes($propName);
        $type = $context->getNativeType($property->getTypeName());
        $nativeType = \addslashes($type);
        $validation = $this->generateDenormalizerPropertyValueValidate($property, $context);
        $ret = [];

        // Attempt eager related class code generation, if possible.
        try {
            if (\class_exists($type)) {
                $normalizerClassName = $this->generateNormalizerClass($type);
                $ret[] = "if (null !== \$value) {";
                $ret[] = "    \$value = \\".$normalizerClassName."::denormalize(\$value, \$context, \$denormalizer);";
                if ($property->isOptional()) {
                    $ret[] = "    if (!(".$validation.")) {";
                    $ret[] = "        Helper\\handle_error(\"Type mismatch\", \$context);";
                    $ret[] = "        \$value = null;";
                    $ret[] = "    }";
                } else {
                    $ret[] = "    if (null === \$value) {";
                    $ret[] = "        Helper\\handle_error(\"Property '{$escapedPropName}' cannot be null\", \$context);";
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
                $ret[] = "if (null !== \$value && \$denormalizer) {";
                $ret[] = "    {$variable} = \$denormalizer('{$nativeType}', \$value, \$context);";
                if ($property->isOptional()) {
                    $ret[] = "    if (!(".$validation.")) {";
                    $ret[] = "        Helper\\handle_error(\"Type mismatch\", \$context);";
                    $ret[] = "        \$value = null;";
                    $ret[] = "    }";
                } else {
                    $ret[] = "    if (null === \$value) {";
                    $ret[] = "        Helper\\handle_error(\"Property '{$escapedPropName}' cannot be null\", \$context);";
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
    private function generateDenormalizerPropertyValue(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
        $handleCode = $this->generateDenormalizerPropertyValueConvert($property, $context, 2, '$value');

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
    private function generateDenormalizerPropertyCollection(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));
        $handleCode1 = $this->generateDenormalizerPropertyValueConvert($property, $context, 3, '$values');
        $handleCode2 = $this->generateDenormalizerPropertyValueConvert($property, $context, 5, '$value');

        $writer->write(<<<EOT
        // Denormalize '{$propName}' collection property
        \$propValue = [];
        \$values = Helper\\find_value(\$input, {$candidateNames}, \$context);
        if (!\is_iterable(\$values)) {
            {$handleCode1}
            \$propValue[] = \$values;
        } else {
            foreach (\$values as \$index => \$value) {
                {$handleCode2}
                \$propValue[\$index] = \$value;
            }
        }
        \$instance->{$propName} = \$propValue;
EOT
        );
    }

    /**
     * Generate property handling code
     */
    private function generateDenormalizerProperty(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        if ($property->isCollection()) {
            $this->generateDenormalizerPropertyCollection($property, $context, $writer);
        } else {
            $this->generateDenormalizerPropertyValue($property, $context, $writer);
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

        $imports = [Context::class, 'MakinaCorpus\Normalizer\Generator\Iterations as Helper'];
        if ($generatedClassNamespace !== $classNamespace) {
            $imports[] = $nativeType;
        }

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
    public static \$normalizer{$index};

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
     *   A callback that will normalize externally handled values, parameters are:
     *      - mixed \$input raw value from denormalized data
     *      - Context \$context the context
     */
    public static function normalize(\$object, Context \$context, ?callable \$normalizer = null): array
    {
        \$ret = [];
EOT
        );

        $writer->write("\n");
        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write("\n");
            $writer->write(<<<EOT
        (self::\$normalizer{$index})(\$ret, \$object, \$context, \$normalizer);
EOT
            );
        }
        $writer->write("\n\n");

        $writer->write(<<<EOT
        return \$ret;
    }

    /**
     * Create and denormalize {$nativeType} instances.
     *
     * @param callable \$normalizer
     *   A callback that will denormalize externally handled values, parameters are:
     *      - string \$type PHP native type
     *      - mixed \$input raw value from normalized data
     *      - Context \$context the context
     */
    public static function denormalize(array \$input, Context \$context, ?callable \$denormalizer = null): {$localClassName}
    {
        \$ret = (new \ReflectionClass({$localClassName}::class))->newInstanceWithoutConstructor();
EOT
        );

        $writer->write("\n");
        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write("\n");
            $writer->write(<<<EOT
        (self::\$denormalizer{$index})(\$ret, \$input, \$context, \$denormalizer);
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
 * Normalizer for properties of {$className}.
 */
{$localClassName}Normalizer::\$normalizer{$index} = \Closure::bind(
    static function (array &\$ret, {$className} \$object, Context \$context, ?callable \$normalizer = null): void {
EOT
            );

            $count = \count($properties);
            foreach ($properties as $property) {
                if (1 === $count) { // Minor useless tweak.
                    $writer->write("\n");
                } else {
                    $writer->write("\n\n");
                }
                $this->generateNormalizerProperty($property, $context, $writer);
            }

            $writer->write("\n");
            $writer->write(<<<EOT
    },
    null, {$className}::class
);

/**
 * Denormalizer for properties of {$className}.
 */
{$localClassName}Normalizer::\$denormalizer{$index} = \Closure::bind(
    static function ({$className} \$instance, array \$input, Context \$context, ?callable \$denormalizer = null): void {
EOT
            );

            $count = \count($properties);
            foreach ($properties as $property) {
                if (1 === $count) { // Minor useless tweak.
                    $writer->write("\n");
                } else {
                    $writer->write("\n\n");
                }
                $this->generateDenormalizerProperty($property, $context, $writer);
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
