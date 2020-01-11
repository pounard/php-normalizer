<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\GeneratorContext;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;
use MakinaCorpus\Normalizer\WritableNormalizerRegistry;
use MakinaCorpus\Normalizer\Generator\Plugin\GeneratorPluginChain;

/**
 * Default normalizer generator.
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

    /** @var WritableNormalizerRegistry */
    private $registry;

    /**
     * Constructor
     *
     * @param string $projectSourceRoot
     */
    public function __construct(
        ContextFactory $contextFactory,
        string $projectSourceDirectory,
        WritableNormalizerRegistry $registry,
        ?string $generatedClassNamespace = null,
        ?Psr4AppNamingStrategy $namingStrategy = null,
        ?GeneratorPluginChain $generatorPluginChain = null
    ) {
        $this->contextFactory = $contextFactory;
        $this->generatedClassNamespace = $generatedClassNamespace;
        $this->generatorPluginChain = $generatorPluginChain ?? new GeneratorPluginChain();
        $this->namingStrategy = $namingStrategy ?? new Psr4AppNamingStrategy();
        $this->projectSourceDirectory = $projectSourceDirectory;
        $this->registry = $registry;
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
        $context = GeneratorContext::of($this->contextFactory->createContext());

        $class = $this->generateClass($className, $normalizerClassName, $context, $writer);
        $this->registry->register($className, $normalizerClassName, $filename, $context->getClassDependencies());

        return $class;
    }

    /**
     * Generate or handle gracefully another type (de)normalizer class.
     */
    private function generateOtherNormalizerClass(PropertyDefinition $property, GeneratorContext $context, string $input): ?string
    {
        $type = $context->getNativeType($property->getTypeName());
        try {
            // If attempt is done with an interface, it will raise an exception.
            $isTerminal = $context->getType($type)->isTerminal();
            if (\class_exists($type) && !$isTerminal) {
                $normalizerClassName = $this->generateNormalizerClass($type);
                $context->addClassDependency($type);
                $context->addClassDependency($normalizerClassName);
                return $normalizerClassName;
            }
        } catch (TypeDoesNotExistError $e) {
            $context->addWarning($e->getMessage());
        }
        return null;
    }

    /**
     * Generate single value normalizer call code.
     */
    private function generateNormalizerCallValue(PropertyDefinition $property, GeneratorContext $context, Writer $writer, string $input): string
    {
        $normalizeCall = null;

        if ($this->generatorPluginChain->supports($property, $context)) {
            $normalizeCall = $this->generatorPluginChain->generateNormalizeCode($property, $context, $input);
        }

        // Attempt eager related class code generation, if possible.
        if (!$normalizeCall) {
            if ($normalizerClassName = $this->generateOtherNormalizerClass($property, $context, $input)) {
                $shortName = $context->addImport($normalizerClassName);
                $normalizeCall = "{$shortName}::normalize({$input}, \$context, \$normalizer)";
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
    private function generateNormalizerPropertyValue(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $normalizedName = \addslashes($property->getNormalizedName());
        $output = "\$ret['{$normalizedName}']";
        $input = "\$object->{$propName}";
        $normalizeCall = $this->generateNormalizerCallValue($property, $context, $writer, $input);

        $writer->write(<<<EOT
{$output} = (null === {$input} ? null : {$normalizeCall});
EOT
        );
    }

    /**
     * Generate value collection property set
     */
    private function generateNormalizerPropertyCollection(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $normalizedName = \addslashes($property->getNormalizedName());
        $output = "\$ret['{$normalizedName}']";
        $arrayInput = "\$object->{$propName}";
        $input = "\$value";
        $normalizeCall = $this->generateNormalizerCallValue($property, $context, $writer, $input);

        $writer->write(<<<EOT
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
    private function generateNormalizerProperty(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
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
    private function generateDeormalizerCallValue(PropertyDefinition $property, GeneratorContext $context, Writer $writer, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());
        $normalizeCall = null;

        if ($this->generatorPluginChain->supports($property, $context)) {
            $normalizeCall = $this->generatorPluginChain->generateDenormalizeCode($property, $context, $input);
        }

        // Attempt eager related class code generation, if possible.
        if (!$normalizeCall) {
            if ($normalizerClassName = $this->generateOtherNormalizerClass($property, $context, $input)) {
                $shortName = $context->addImport($normalizerClassName);
                $normalizeCall = "{$shortName}::denormalize({$input}, \$context, \$denormalizer)";
            }
        }

        $isBuiltIn = !\class_exists($type) && !\interface_exists($type);
        $shortName = $isBuiltIn ? null : $context->addImport($type);

        if (!$normalizeCall) {
            if (!$isBuiltIn || 'null' === $type) {
                $typeString = "'{$type}'";
            } else {
                $typeString = $shortName.'::class';
            }
            $normalizeCall = "(\$denormalizer ? \$denormalizer({$typeString}, {$input}, \$context, \$denormalizer) : {$input})";
        }

        // Allow already denormalized objects to pass through.
        if (!$isBuiltIn) {
            return <<<EOT
({$input} instanceof $shortName
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
    private function generateDenormalizerPropertyValueWithCandidates(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));

        $output = "\$instance->{$propName}";
        $input = "\$value";
        $denormalizeCall = $this->generateDeormalizerCallValue($property, $context, $writer, $input);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));
        $helperClassName = $context->addImport(RuntimeHelper::class);

        if ($property->isOptional()) {
            // Nullable properties can have a default value:
            //   - if we find null, we must ensure there was an explicit null,
            //   - if there is no explicit null, leave the default value as-is.
            $denormalizeCall = $writer::indent($denormalizeCall, 2, true);
            $writer->write(<<<EOT
\$option = {$helperClassName}::find(\$input, {$candidateNames}, \$context);
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
            $denormalizeCall = $writer::indent($denormalizeCall, 1, true);
            $writer->write(<<<EOT
\$option = {$helperClassName}::find(\$input, {$candidateNames}, \$context);
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
    private function generateDenormalizerPropertyValue(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
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
            // Nullable properties can have a default value:
            //   - if we find null, we must ensure there was an explicit null,
            //   - if there is no explicit null, leave the default value as-is.
            $writer->write(<<<EOT
{$output} = isset($input) ? {$denormalizeCall} : null;
EOT
            );
        } else {
            $denormalizeCall = $writer::indent($denormalizeCall, 1, true);
            $writer->write(<<<EOT
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
    private function generateDenormalizerPropertyCollectionWithCandidates(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
    {
        $propName = \addslashes($property->getNativeName());
        $candidateNames = \sprintf("['%s']", implode("', '", \array_map('\addslashes', $property->getCandidateNames())));

        $output = "\$instance->{$propName}";
        $input = "\$value";
        $arrayInput = "\$values";
        $denormalizeCall = $this->generateDeormalizerCallValue($property, $context, $writer, $input);
        $denormalizeCall = $writer::indent($denormalizeCall, 4, true);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));
        $helperClassName = $context->addImport(RuntimeHelper::class);

        $writer->write(<<<EOT
\$option = {$helperClassName}::find(\$input, {$candidateNames}, \$context);
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
    private function generateDenormalizerPropertyCollection(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
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
        $denormalizeCall = $writer::indent($denormalizeCall, 4, true);

        $escapedNativeType = \addslashes($context->getNativeType($property->getTypeName()));

        $writer->write(<<<EOT
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
    private function generateDenormalizerProperty(PropertyDefinition $property, GeneratorContext $context, Writer $writer): void
    {
        if ($property->isCollection()) {
            $this->generateDenormalizerPropertyCollection($property, $context, $writer);
        } else {
            $this->generateDenormalizerPropertyValue($property, $context, $writer);
        }
    }

    /**
     * Generate (de)normalizer class.
     *
     * @return string
     *   The generated class fully qualified name
     */
    private function generateClass(string $type, string $generatedClassName, GeneratorContext $context, Writer $writer): string
    {
        $typeDef = $context->getType($type);
        $nativeType = $context->getNativeType($type);

        if ($typeDef->isTerminal()) {
            throw new \LogicException(\sprintf("Cannot dump normalizer: type '%s' is terminal", $nativeType));
        }
        if (!\class_exists($nativeType)) {
            throw new \LogicException(\sprintf("Cannot dump normalizer: type '%s' does not exist or is not a class", $nativeType));
        }

        $classNamespace = RuntimeHelper::getClassNamespace($nativeType);
        $generatedLocalClassName = RuntimeHelper::getClassShortName($generatedClassName);
        $generatedClassNamespace = RuntimeHelper::getClassNamespace($generatedClassName);

        $memoryWriter = Writer::memory();
        $context->addImport(Context::class);
        if ($generatedClassNamespace !== $classNamespace) {
            $context->addImport($nativeType);
        }

        $this->generateClassBody($type, $generatedClassName, $context, $memoryWriter);

        $imports = \array_filter(
            $context->getImports(),
            static function ($importedClassName) use ($generatedClassName) {
                return !RuntimeHelper::inSameNamespace($importedClassName, $generatedClassName);
            }
        );
        \asort($imports);
        foreach ($imports as $alias => $className) {
            if ($alias !== RuntimeHelper::getClassShortName($className)) {
                $imports[$alias] = $className." as ".$alias;
            }
        }
        $importsAsString = "use ".\implode(";\nuse ", $imports).';';

        $classBody = $memoryWriter->getBuffer();

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
{$classBody}
EOT
        );

        // Weird thing, should fix this, class closure is handled within the next
        // generateClassBody() method call, to avoid having to propagate the map
        // of properties computed there.

        return $generatedClassNamespace.'\\'.$generatedLocalClassName;
    }

    /**
     * Generate class body.
     */
    private function generateClassBody(string $type, string $generatedClassName, GeneratorContext $context, Writer $writer): void
    {
        $typeDef = $context->getType($type);
        $nativeType = $context->getNativeType($type);

        $parts = \array_filter(\explode('\\', $nativeType));
        $localClassName = \array_pop($parts);

        // Create a property map with declaring classes and access scopes.
        $perClassMap = [];
        $index = 0;
        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
        foreach ($properties = $typeDef->getProperties() as $property) {
            $declaringClass = $property->getDeclaringClass();
            // Only create a new closure for when the property cannot be
            // accessed by the hydrated class.
            if ('private' === $property->getDeclaredScope() && $declaringClass !== $nativeType) {
                $localDeclaringClassName = $context->addImport($declaringClass);
                $perClassMap[$localDeclaringClassName][] = $property;
            } else {
                $perClassMap[$localClassName][] = $property;
            }
        }

        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write(<<<EOT
    /** @var callable */
    public static \$normalizer{$index};

    /** @var callable */
    public static \$denormalizer{$index};
EOT
            );
            $writer->newline();
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

        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write(<<<EOT
        (self::\$normalizer{$index})(\$ret, \$object, \$context, \$normalizer);
EOT
            );
        }
        $writer->newline();

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

        for ($index = 0; $index < \count($perClassMap); $index++) {
            $writer->write(<<<EOT
        (self::\$denormalizer{$index})(\$ret, \$input, \$context, \$denormalizer);
EOT
            );
        }
        $writer->newline();

        $writer->write(<<<EOT
        return \$ret;
    }
}
EOT
        );

        $index = 0;
        foreach ($perClassMap as $className => $properties) {
            $writer->newline();
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
                if (1 !== $count) { // Minor useless tweak.
                    $writer->newline();
                }
                /*
                $escapedPropertyName = \addslashes($property->getNativeName());
                $writer->write(<<<EOT
        try {
            \$context->enter('{$escapedPropertyName}');
EOT
                );
                try {
                    $writer->indentationReset(3);
                    $this->generateNormalizerProperty($property, $context, $writer);
                } finally {
                    $writer->indentationReset();
                }
                $writer->write(<<<EOT
        } finally {
            \$context->leave();
        }
EOT
                );
                 */
                try {
                    $writer->indentationReset(2);
                    $this->generateNormalizerProperty($property, $context, $writer);
                } finally {
                    $writer->indentationReset();
                }
            }

            $writer->newline();
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
                if (1 !== $count) { // Minor useless tweak.
                    $writer->newline();
                }
                /*
                $escapedPropertyName = \addslashes($property->getNativeName());
                $writer->write(<<<EOT
        try {
            \$context->enter('{$escapedPropertyName}');
EOT
                );
                try {
                    $writer->indentationReset(3);
                    $this->generateDenormalizerProperty($property, $context, $writer);
                } finally {
                    $writer->indentationReset();
                }
                $writer->write(<<<EOT
        } finally {
            \$context->leave();
        }
EOT
                );
                 */

                try {
                    $writer->indentationReset(2);
                    $this->generateDenormalizerProperty($property, $context, $writer);
                } finally {
                    $writer->indentationReset();
                }
            }

            $writer->newline();
            $writer->write(<<<EOT
    },
    null, {$className}::class
);
EOT
            );
            $index++;
        }
    }
}
