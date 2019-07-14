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
final class Context
{
    private $alwaysGuessType = false;
    private $circularReferenceLimit = 1;
    private $typeMap = [];
    private $depth = 0;
    private $groups = [];
    private $options = [];
    private $strict = false;
    private $symfonyCompatibility = false;
    private $visitedObjectMap = [];

    /**
     * Default constructor.
     *
     * @param TypeDefinitionMap $typeMap
     *   Type definition map, preferably a cached/preloaded
     *   implementation for performances
     * @param array $options
     *   Arbitrary array of options, see Option, NormalizeOption and
     *   DenormalizeOption class constants for available options and
     *   documentation.
     * @param bool $symfonyCompatibility
     *   Set this to true to activate symfony/serializer component compability.
     */
    public function __construct(TypeDefinitionMap $typeMap, array $options = [], bool $symfonyCompatibility = false)
    {
        $this->alwaysGuessType = (bool)($options[NormalizeOption::ALWAYS_GUESS_TYPE] ?? false);
        $this->options = $options;
        $this->symfonyCompatibility = $symfonyCompatibility;
        $this->typeMap = $typeMap;

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
     * Should normalization process attempt to guess all types at runtime
     * instead of 
     */
    public function shouldAlwaysGuessTypes(): bool
    {
        return $this->alwaysGuessType;
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
     * @internal
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @internal
     */
    public function enter(): void
    {
        $this->depth++;
    }

    /**
     * @internal
     */
    public function leave(): void
    {
        $this->depth--;
    }

    /**
     * Creates a new instance without state
     */
    public function fresh()
    {
        $instance = clone $this;
        $instance->visitedObjectMap = [];
        $instance->depth = 0;

        return $instance;
    }
}
