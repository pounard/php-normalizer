<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * (de)normalization validation result
 */
interface ValidationResult
{
    /**
     * Is current context valid.
     */
    public function isValid(): bool;

    /**
     * Get all errors.
     *
     * @return string[][]
     *   Keys are property path, values are error messages
     */
    public function getErrors(): array;

    /**
     * Get all warnings.
     *
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
    public function addError(string $message, bool $recoverable = false): void;

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
    public function getPath(): ?string;

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
    public function getPath(): ?string
    {
        if (!$this->currentPath && $this->currentContext) {
            return $this->currentPath = \implode(self::PATH_SEP, $this->currentContext);
        }
        return $this->currentPath;
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
    public function addError(string $message, bool $recoverable = false): void
    {
        $this->errors[$this->getPath() ?? '<root>'][] = $message;
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
