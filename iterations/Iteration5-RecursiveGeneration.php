<?php
/**
 * Iteration #5.
 *
 * Generate code recursively stopping on blacklists.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Iterations;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;
use MakinaCorpus\Normalizer\Generator\Generator;
use MakinaCorpus\Normalizer\Generator\NamingStrategy;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Generator\Writer;

/**
 * Normalizer
 */
final class Normalizer5
{
    /** @var Generator */
    private $generator;

    /**
     * Constructor
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Dernormalise object
     */
    public function denormalize(string $type, /* string|array|T */ $input, Context $context) /* : T */
    {
        $nativeType = $context->getNativeType($type);

        $external = hydrator1_external_implementation($nativeType, $input, $context);
        if ($external->handled) {
            return $external->value;
        }

        $normalizer = $this->generator->getNormalizerClass($nativeType);

        if (!$normalizer) {
            throw new \RuntimeException("Implemeent me");
        }

        return \call_user_func(
            [$normalizer, 'denormalize'],
            $input,
            $context,
            [$this, 'denormalize']
        );
    }
}

/**
 * Generator wrapper, with naming strategy
 */
final class Generator5Runtime implements Generator
{
    /** @var NamingStrategy */
    private $namingStrategy;

    /** @var string[] */
    private $nameMap = [];

    /**
     * Constructor
     *
     * @param string $projectSourceRoot
     */
    public function __construct(?NamingStrategy $namingStrategy)
    {
        if (!$namingStrategy) {
            $namingStrategy = new Psr4AppNamingStrategy();
        }
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizerClass(string $className): ?string
    {
        $normalizerClass = $this->nameMap[$className] ?? null;
        if ($normalizerClass) {
            return $normalizerClass;
        }
        if (false === $normalizerClass) {
            return null;
        }

        $normalizerClass = $this->namingStrategy->generateClassName($className, '\\');

        if (!\class_exists($normalizerClass)) {
            // We store booleans in the array otherwise isset() could return
            // null, we should then user \array_key_exists(), but it is way
            // slower to execute.
            $this->nameMap[$normalizerClass] = false;

            return null;
        }

        return $normalizerClass;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizerClass(string $className): string
    {
        throw new \LogicException("You cannot wake up the generator at runtime");
    }
}

/**
 * Generator wrapper, with naming strategy
 */
final class Generator5Impl implements Generator
{
    /** @var ContextFactory */
    private $contextFactory;

    /** @var NamingStrategy */
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
        $this->namingStrategy = new Psr4AppNamingStrategy('Normalizer', 'Generated5');
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
                return "null === \$value || \\MakinaCorpus\Normalizer\\RuntimeHelper::getType(\$value) === '".$nativeType."'";
            } else {
                return "\\MakinaCorpus\Normalizer\\RuntimeHelper::getType(\$value) === '".$nativeType."'";
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
        \call_user_func(self::\$accessor, \$ret, '{$propName}', \$value);
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
        \call_user_func(self::\$accessor, \$ret, '{$propName}', \$propValue);
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

use MakinaCorpus\Normalizer\Generator\Iterations as Helper;

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
                $this->generateProperty($property, $context, $writer);
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

        return $generatedClassNamespace.'\\'.$generatedLocalClassName;
    }
}
