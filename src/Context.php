<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

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
    private $verbose = false; // @todo fallabck to false.
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
     * Property can not be null
     */
    public function nullValueError(string $expected): void
    {
        $expected = $this->getNativeType($expected);
        if ($path = $this->getPath()) {
            $message = \sprintf("'%s' property cannot be null, expected '%s'", $path, $expected);
        } else {
            $message = \sprintf("property cannot be null, expected '%s'", $expected);
        }
        if (!$this->verbose) {
            throw new NullValueTypeError($message);
        }
        $this->addError($message, true); // Do not raise exception.
    }

    /**
     * Unexpected type error
     */
    public function typeMismatchError(string $expected, string $real)
    {
        $expected = $this->getNativeType($expected);
        $real = $this->getNativeType($real);
        if ($path = $this->getPath()) {
            $message = \sprintf("'%s': type mismatch, expected '%s' got '%s'", $path, $expected, $real);
        } else {
            $message = \sprintf("type mismatch: expected '%s' got '%s'", $expected, $real);
        }
        if (!$this->verbose) {
            throw new NullValueTypeError($message);
        }
        $this->addError($message, false); // This is NOT recoverable.
    }

    /**
     * Property can not be null
     */
    public function classDoesNotExistError(string $className): void
    {
        if ($path = $this->getPath()) {
            $message = \sprintf("'%s': '%s' class does not exist", $path, $className);
        } else {
            $message = \sprintf("'%s' class does not exist", $className);
        }
        $this->addError($message, false); // This is NOT recoverable.
    }

    /**
     * {@inheritdoc}
     */
    public function addError(string $message, bool $recoverable = false): void
    {
        if (!$this->verbose && !$recoverable) {
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
    public function getPath(): ?string
    {
        return $this->validationResult->getPath();
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
    public function __construct(?TypeDefinitionMap $typeMap = null)
    {
        $this->typeMap = $typeMap ?? new ReflectionTypeDefinitionMap();
    }

    /**
     * Create context instance
     */
    public function createContext(array $options = [], bool $symfonyCompatibility = false): Context
    {
        return new Context($this->typeMap, $options, $symfonyCompatibility);
    }
}
