<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;
use MakinaCorpus\Normalizer\Generator\Plugin\GeneratorPluginChain;

/**
 * Default normalizer generator.
 *
 * @todo
 *  - inject naming strategy
 *  - make it more configurable
 *  - make it pluggable
 */
final class DefaultGenerator implements Generator
{
    /** @var ContextFactory */
    private $contextFactory;

    /** @var \MakinaCorpus\Normalizer\Generator\NamingStrategy */
    private $namingStrategy;

    /** @var ?string */
    private $generatedClassNamespace;

    /** @var string */
    private $projectSourceDirectory;

    /** @var GeneratorPluginChain */
    private $generatorPluginChain;

    /**
     * Constructor
     *
     * @param string $projectSourceRoot
     */
    public function __construct(
        ContextFactory $contextFactory,
        string $projectSourceDirectory,
        ?string $generatedClassNamespace = null,
        ?Psr4AppNamingStrategy $namingStrategy = null,
        ?GeneratorPluginChain $generatorPluginChain = null
    ) {
        $this->contextFactory = $contextFactory;
        $this->generatedClassNamespace = $generatedClassNamespace;
        $this->namingStrategy = $namingStrategy ?? new Psr4AppNamingStrategy();
        $this->projectSourceDirectory = $projectSourceDirectory;
        $this->generatorPluginChain = $generatorPluginChain ?? new GeneratorPluginChain();
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
        $normalizerClassName = $this->namingStrategy->generateClassName($className, $this->generatedClassNamespace ?? '\\');
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
     * Generate single value normalizer call code.
     */
    private function generateNormalizerCallValue(PropertyDefinition $property, Context $context, Writer $writer, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());
        $normalizeCall = null;

        if ($this->generatorPluginChain->supports($property, $context)) {
            $normalizeCall = $this->generatorPluginChain->generateNormalizeCode($property, $context, $input);
        }

        // Attempt eager related class code generation, if possible.
        if (!$normalizeCall) {
            try {
                // If attempt is done with an interface, it will raise an exception.
                $isTerminal = $context->getType($type)->isTerminal();
                if (\class_exists($type) && !$isTerminal) {
                    $normalizerClassName = $this->generateNormalizerClass($type);
                    $normalizeCall = "\\{$normalizerClassName}::normalize({$input}, \$context, \$normalizer)";
                }
            } catch (TypeDoesNotExistError $e) {
                $context->addWarning($e->getMessage());
            }
        }

        if (!$normalizeCall) {
            $normalizeCall = "(\$normalizer ? \$normalizer({$input}, \$context, \$normalizer) : {$input})";
        }

        return $normalizeCall;
    }

    /**
     * Generate single value property set
     */
    private function generateNormalizerPropertyValue(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $normalizedName = \addslashes($property->getNormalizedName());
        $output = "\$ret['{$normalizedName}']";
        $input = "\$object->{$propName}";
        $normalizeCall = $this->generateNormalizerCallValue($property, $context, $writer, $input);

        $writer->write(<<<EOT
        // Normalize '{$propName}' property
        {$output} = (null === {$input} ? null : {$normalizeCall});
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
        $output = "\$ret['{$normalizedName}']";
        $arrayInput = "\$object->{$propName}";
        $input = "\$value";
        $normalizeCall = $this->generateNormalizerCallValue($property, $context, $writer, $input);

        $writer->write(<<<EOT
        // Normalize '{$propName}' property
        {$output} = [];
        if ({$arrayInput}) {
            foreach ({$arrayInput} as \$index => {$input}) {
                if (null === {$input}) {
                    {$output}[\$index] = null;
                } else {
                    {$output}[\$index] = {$normalizeCall};
                }
            }
        }
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
     * Generate single value normalizer call code.
     */
    private function generateDeormalizerCallValue(PropertyDefinition $property, Context $context, Writer $writer, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());
        $normalizeCall = null;

        if ($this->generatorPluginChain->supports($property, $context)) {
            $normalizeCall = $this->generatorPluginChain->generateDenormalizeCode($property, $context, $input);
        }

        // Attempt eager related class code generation, if possible.
        if (!$normalizeCall) {
            try {
                // If attempt is done with an interface, it will raise an exception.
                $isTerminal = $context->getType($type)->isTerminal();
                if (\class_exists($type) && !$isTerminal) {
                    $normalizerClassName = $this->generateNormalizerClass($type);
                    $normalizeCall = "\\{$normalizerClassName}::denormalize({$input}, \$context, \$denormalizer)";
                }
            } catch (TypeDoesNotExistError $e) {
                $context->addWarning($e->getMessage());
            }
        }

        $isBuiltIn = !\class_exists($type) && !\interface_exists($type);
        $classFqdn = $isBuiltIn ? null : '\\'.\ltrim($type, '\\');

        if (!$normalizeCall) {
            if (!$isBuiltIn) {
                $typeString = "'{$type}'";
            } else {
                $typeString = $classFqdn.'::class';
            }
            $normalizeCall = "(\$denormalizer ? \$denormalizer({$typeString}, {$input}, \$context, \$denormalizer) : {$input})";
        }

        // Allow already denormalized objects to pass through.
        if (!$isBuiltIn) {
            return <<<EOT
({$input} instanceof $classFqdn
    ? {$input}
    : {$normalizeCall}
)
EOT;
        }

        return $normalizeCall;
    }

    /**
     * Generate call that find values into the input array when there are more than one candidate.
     */
    private function generateDenormalizerPropertyValueWithCandidates(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));

        $output = "\$instance->{$propName}";
        $input = "\$value";
        $denormalizeCall = $this->generateDeormalizerCallValue($property, $context, $writer, $input);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));

        if ($property->isOptional()) {
            $denormalizeCall = $writer->indent($denormalizeCall, 4, true);
            // Nullable properties can have a default value:
            //   - if we find null, we must ensure there was an explicit null,
            //   - if there is no explicit null, leave the default value as-is.
            $writer->write(<<<EOT
        // Denormalize '{$propName}' nullable property
        \$option = Helper::find(\$input, {$candidateNames}, \$context);
        if (\$option->success) {
            {$input} = \$option->value;
            if (null === {$input}) {
                {$output} = null;
            } else {
                {$output} = {$denormalizeCall};
            }
        }
EOT
            );
        } else {
            $denormalizeCall = $writer->indent($denormalizeCall, 3, true);
            $writer->write(<<<EOT
        // Denormalize '{$propName}' required property
        \$option = Helper::find(\$input, {$candidateNames}, \$context);
        {$input} = \$option->value;
        if (!\$option->success || null === {$input}) {
            \$context->nullValueError('{$escapedNativeType}');
        } else {
            {$output} = {$denormalizeCall};
        }
EOT
            );
        }
    }

    /**
     * Generate single value property set.
     */
    private function generateDenormalizerPropertyValue(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $candidateNames = $property->getCandidateNames();
        if (1 < \count($candidateNames)) {
            $this->generateDenormalizerPropertyValueWithCandidates($property, $context, $writer);
            return;
        }

        $propName = \addslashes($property->getNativeName());
        $inputKey = \addslashes(\reset($candidateNames));

        $output = "\$instance->{$propName}";
        $input = "\$input['{$inputKey}']";
        $denormalizeCall = $this->generateDeormalizerCallValue($property, $context, $writer, $input);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));

        if ($property->isOptional()) {
            $denormalizeCall = $writer->indent($denormalizeCall, 2, true);
            // Nullable properties can have a default value:
            //   - if we find null, we must ensure there was an explicit null,
            //   - if there is no explicit null, leave the default value as-is.
            $writer->write(<<<EOT
        // Denormalize '{$propName}' nullable property
        {$output} = isset($input) ? {$denormalizeCall} : null;
EOT
            );
        } else {
            $denormalizeCall = $writer->indent($denormalizeCall, 3, true);
            $writer->write(<<<EOT
        // Denormalize '{$propName}' required property
        if (!isset($input)) {
            \$context->nullValueError('{$escapedNativeType}');
        } else {
            {$output} = {$denormalizeCall};
        }
EOT
            );
        }
    }

    /**
     * Generate value collection property set when there are more than one candidate.
     */
    private function generateDenormalizerPropertyCollectionWithCandidates(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));

        $output = "\$instance->{$propName}";
        $input = "\$value";
        $arrayInput = "\$values";
        $denormalizeCall = $this->generateDeormalizerCallValue($property, $context, $writer, $input);
        $denormalizeCall = $writer->indent($denormalizeCall, 6, true);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));

        $writer->write(<<<EOT
        // Denormalize '{$propName}' collection property
        \$option = Helper::find(\$input, {$candidateNames}, \$context);
        if (\$option->success && ({$arrayInput} = \$option->value)) {
            if (!\is_iterable({$arrayInput})) {
                {$arrayInput} = (array){$arrayInput};
            }
            if ({$arrayInput}) {
                {$output} = [];
                foreach ({$arrayInput} as \$index => {$input}) {
                    if (null === {$input}) {
                        \$context->nullValueError('{$escapedNativeType}');
                        {$output}[\$index] = null;
                    } else {
                        {$output}[\$index] = {$denormalizeCall};
                    }
                }
            }
        }
EOT
        );
    }

    /**
     * Generate value collection property set
     */
    private function generateDenormalizerPropertyCollection(PropertyDefinition $property, Context $context, Writer $writer): void
    {
        $candidateNames = $property->getCandidateNames();
        if (1 < \count($candidateNames)) {
            $this->generateDenormalizerPropertyCollectionWithCandidates($property, $context, $writer);
            return;
        }

        $propName = \addslashes($property->getNativeName());
        $inputKey = \addslashes(\reset($candidateNames));

        $output = "\$instance->{$propName}";
        $input = "\$value";
        $arrayInput = "\$input['{$inputKey}']";
        $denormalizeCall = $this->generateDeormalizerCallValue($property, $context, $writer, $input);
        $denormalizeCall = $writer->indent($denormalizeCall, 6, true);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));

        $writer->write(<<<EOT
        // Denormalize '{$propName}' collection property
        if (isset($arrayInput)) {
            if (!\is_iterable({$arrayInput})) {
                {$arrayInput} = (array){$arrayInput};
            }
            if ({$arrayInput}) {
                {$output} = [];
                foreach ({$arrayInput} as \$index => {$input}) {
                    if (null === {$input}) {
                        \$context->nullValueError('{$escapedNativeType}');
                        {$output}[\$index] = null;
                    } else {
                        {$output}[\$index] = {$denormalizeCall};
                    }
                }
            }
        }
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

        $imports = [Context::class];
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
     * Normalize \\{$nativeType} instance into an array.
     *
     * @param callable \$normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::normalize()
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
     * Create and denormalize an \\{$nativeType} instance.
     *
     * @param callable \$normalizer
     *   Signature is \MakinaCorpus\Normalizer\Normalizer::denormalize()
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
