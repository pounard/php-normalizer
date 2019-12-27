<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Front access point for the API
 */
final class ContextFactory
{
    /** @var TypeDefinitionMap */
    private $typeMap;

    /**
     * Default constructor
     */
    public function __construct(TypeDefinitionMap $typeMap)
    {
        $this->typeMap = $typeMap;
    }

    /**
     * Create context instance
     */
    public function createContext(array $options = [], bool $symfonyCompatibility = false): Context
    {
        return new Context($this->typeMap, $options, $symfonyCompatibility);
    }
}

/**
 * (de)normalization validation result
 */
interface ValidationResult
{
    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return string[][]
     *   Keys are property path, values are error messages
     */
    public function getErrors(): array;

    /**
     * @return string[][]
     *   Keys are property path, values are error messages
     */
    public function getWarnings(): array;
}

/**
 * (de)normalization validation result builder
 */
interface ValidationResultBuilder
{
    /**
     * Add error
     */
    public function addError(string $message): void;

    /**
     * Add warning
     */
    public function addWarning(string $message): void;

    /**
     * Get current depth
     */
    public function getDepth(): int;

    /**
     * Get current path
     */
    public function getPath(): string;

    /**
     * Enter property in current context
     */
    public function enter(string $propName): void;

    /**
     * Leave current context
     *
     * @throws \LogicException
     *   A fatal error when trying to leave the first level
     */
    public function leave(): void;
}

/**
 * Default implementation for ValidationResultBuilder
 */
final class DefaultValidationResultBuilder implements ValidationResultBuilder, ValidationResult
{
    const PATH_SEP = '.';
    const UNKNOW_PROP_NAME = 'unknown';

    /** @var int */
    private $depth = 0;

    /** @var string[] */
    private $currentContext = [];

    /** @var string[][] */
    private $errors = [];

    /** @var string[][] */
    private $warnings = [];

    /** @var string */
    private $currentPath;

    /**
     * {@inheritdoc}
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * Get current path
     */
    public function getPath(): string
    {
        if (!$this->currentPath && $this->currentContext) {
            return $this->currentPath = \implode(self::PATH_SEP, $this->currentContext);
        }
        return $this->currentPath ?? '(none)';
    }

    /**
     * {@inheritdoc}
     */
    public function enter(?string $propName = null): void
    {
        $this->currentContext[] = $propName ?? self::UNKNOW_PROP_NAME;
        $this->currentPath = null;
        $this->depth++;
    }

    /**
     * {@inheritdoc}
     */
    public function leave(): void
    {
        if (0 === $this->depth) {
            throw new \LogicException("Cannot leave when depth is already 0");
        }

        \array_pop($this->currentContext);

        $this->currentPath = null;
        $this->depth--;
    }

    /**
     * {@inheritdoc}
     */
    public function addError(string $message): void
    {
        $this->errors[$this->getPath()][] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(string $message): void
    {
        $this->warnings[$this->getPath()][] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(): bool
    {
        return !$this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }
}

/**
 * (De)normalization and (de)serialization context.
 *
 * It carries configuration, classes and types definitions and it handles
 * the circular dependencies.
 *
 * It can provide compatibility with all options from symfony/serializer
 * component, and it will behave (almost) exactly the same.
 */
final class Context implements ValidationResultBuilder
{
    private $alwaysGuessType = false;
    private $circularReferenceLimit = 1;
    private $groups = [];
    private $options = [];
    private $strict = false;
    private $verbose = false;
    private $symfonyCompatibility = false;
    private $typeMap = [];
    private $validationResult;
    private $visitedObjectMap = [];

    /**
     * Default constructor.
     *
     * @param ?TypeDefinitionMap $typeMap
     *   Type definition map, preferably a cached/preloaded
     *   implementation for performances
     * @param array $options
     *   Arbitrary array of options, see Option, NormalizeOption and
     *   DenormalizeOption class constants for available options and
     *   documentation.
     * @param bool $symfonyCompatibility
     *   Set this to true to activate symfony/serializer component compability.
     */
    public function __construct(?TypeDefinitionMap $typeMap = null, array $options = [], bool $symfonyCompatibility = false)
    {
        $this->alwaysGuessType = (bool)($options[NormalizeOption::ALWAYS_GUESS_TYPE] ?? false);
        $this->options = $options;
        $this->symfonyCompatibility = $symfonyCompatibility;
        $this->typeMap = $typeMap ?? self::createDefaultTypeDefinitionMap();
        $this->validationResult = new DefaultValidationResultBuilder();

        // Do some validation.
        if (isset($options[NormalizeOption::CIRCULAR_REFERENCE_HANDLER]) &&
            !\is_callable($options[NormalizeOption::CIRCULAR_REFERENCE_HANDLER])
        ) {
            throw new InvalidOptionValueError(\sprintf(
                "'%s' option must be callable",
                NormalizeOption::CIRCULAR_REFERENCE_HANDLER
            ));
        }

        if (isset($options[NormalizeOption::CIRCULAR_REFERENCE_LIMIT])) {
            $this->circularReferenceLimit = (int)$options[NormalizeOption::CIRCULAR_REFERENCE_LIMIT];
        }
    }

    /**
     * Will attempt to create a type definition map depending upon the
     * environment capabilities, using sensible defaults.
     */
    public static function createDefaultTypeDefinitionMap(): TypeDefinitionMap
    {
        return new MemoryTypeDefinitionMapCache([new ReflectionTypeDefinitionMap()]);
    }

    /**
     * Convert to Symfony context
     */
    public function toSymfonyContext(): array
    {
        return $this->options;
    }

    /**
     * Get targetted serialization format
     */
    public function getFormat(): string
    {
        return $this->options[Option::SERIALIATION_FORMAT] ?? 'json';
    }

    /**
     * Get option value
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Call custom circular reference handler
     */
    public function handleCircularReference(string $type, $object)
    {
        if (isset($this->options[NormalizeOption::CIRCULAR_REFERENCE_HANDLER])) {
            if ($this->symfonyCompatibility) {
                return \call_user_func(
                    $this->options[NormalizeOption::CIRCULAR_REFERENCE_HANDLER],
                    $object, $this->getFormat(), $this->toSymfonyContext()
                );
            }

            return \call_user_func(
                $this->options[NormalizeOption::CIRCULAR_REFERENCE_HANDLER],
                $type, $object, $this
            );
        }

        throw new CircularDependencyDetectedError("Circular dependency detected.");
    }

    /**
     * Handles circular dependency
     */
    public function isCircularReference($object): bool
    {
        if (!\is_object($object)) {
            return false;
        }
        if (!$this->circularReferenceLimit) {
            return false;
        }

        $hash = \spl_object_hash($object);
        if (isset($this->visitedObjectMap[$hash])) {
            $count = ++$this->visitedObjectMap[$hash];
        } else {
            $count = $this->visitedObjectMap[$hash] = 1;
        }

        return $count > $this->circularReferenceLimit;
    }

    /**
     * Get type definition.
     */
    public function getType(string $type): TypeDefinition
    {
        return $this->typeMap->get($type);
    }

    /**
     * Do alias lookup and get native type name
     */
    public function getNativeType(string $name): string
    {
        return $this->typeMap->getNativeType($name);
    }

    /**
     * When in strict mode, missing non nullable properties will raise
     * errors upon (de)normalization.
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Verbose mode means that we try to analyse everything at the cost
     * of performances when normalizing or denormalizing, it also means
     * that we give much more meaningful errors to end user.
     *
     * This mode recommended for things such as REST API.
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * Creates a new instance without state
     */
    public function fresh()
    {
        $instance = clone $this;
        $instance->currentContext = [];
        $instance->depth = 0;
        $instance->validationResult = new DefaultValidationResultBuilder();
        $instance->visitedObjectMap = [];

        return $instance;
    }

    /**
     * Get validation result
     */
    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }

    /**
     * {@inheritdoc}
     */
    public function addError(string $message): void
    {
        if (!$this->verbose) {
            throw new RuntimeError(\sprintf("%s: %s", $this->validationResult->getPath(), $message));
        }
        $this->validationResult->addError($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth(): int
    {
        return $this->validationResult->getDepth();
    }

    /**
     * Get current path
     */
    public function getPath(): string
    {
        $this->validationResult->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function leave(): void
    {
        $this->validationResult->leave();
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(string $message): void
    {
        $this->validationResult->addWarning($message);
    }

    /**
     * {@inheritdoc}
     */
    public function enter(?string $propName = null): void
    {
        $this->validationResult->enter($propName);
    }
}
