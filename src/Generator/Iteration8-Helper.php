<?php
/**
 * Iteration #8 - helpers.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Normalization helper
 */
final class Helper
{
    /**
     * Alias of \gettype() which returns PHP type hints.
     */
    public static function getType($value): string
    {
        if (\is_object($value)) {
            return \get_class($value);
        }
        $type = \gettype($value);
        if ('integer' === $type) {
            return 'int';
        }
        if ('double' === $type) {
            return 'float';
        }
        return $type;
    }

    /**
     * Format type mismatch error
     */
    public static function typeMismatchError(string $expected, $input): string
    {
        return \sprintf("Type mismatch, expected '%s', got '%s'", $expected, self::getType($input));
    }

    /**
     * Handle error
     *
     * @param string $message
     *   Error messge
     * @param ?Context $context
     *   Context if available
     *
     * @throws \InvalidArgumentException
     *   If there's not context provided
     */
    public function error(string $message, ?Context $context = null): void
    {
        if ($context) {
            $context->addError($message);
        } else {
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Find value in given array
     *
     * @param mixed[] $input
     *   Arbitrary input values
     * @param string[] $candidates
     *   Allowed names for the value, first one will be returned, error will be
     *   raised in case more than one matches the candidates
     * @param ?Context $context
     *   Context if available
     */
    public static function find(array $input, array $candidates, ?Context $context = null): ValueOption
    {
        $verbose = $context ? $context->isVerbose() : false;
        $found = $value = null;

        foreach ($candidates as $name) {
            if (\array_key_exists($name, $input)) {
                if ($found) {
                    self::error(\sprintf("Value found '%s' but was already found in '%s'", $found, $name), $context);
                } else if ($verbose) {
                    $found = $name;
                    $value = $value;
                } else {
                    return ValueOption::ok($input[$name]);
                }
            }
        }

        if ($found) {
            return ValueOption::ok($value);
        }

        return ValueOption::miss();
    }

    /**
     * Convert any value to string if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     * @param ?Context $context
     *   Context if available
     */
    public static function toString($input, ?Context $context = null): ?string
    {
        if (\is_string($input)) {
            return $input;
        } else if (\is_object($input) && \method_exists($input, '__toString()')) {
            return $input->__toString();
        }
        self::error(self::typeMismatchError('string', $input), $context);
        return null;
    }

    /**
     * Convert any value to bool if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     * @param ?Context $context
     *   Context if available
     */
    public static function toBool($input, ?Context $context = null): ?bool
    {
        if (\is_bool($input)) {
            return $input;
        }
        if (\is_int($input)) {
            return (bool)$input;
        }
        if (\is_string($input) && \ctype_digit($input)) {
            return (bool)(int)$input;
        }
        self::error(self::typeMismatchError('bool', $input), $context);
        return null;
    }

    /**
     * Convert any value to int if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     * @param ?Context $context
     *   Context if available
     */
    public static function toInt($input, ?Context $context = null): ?int
    {
        if (\is_int($input)) {
            return $input;
        }
        if (\is_string($input) && \ctype_digit($input)) {
            return (int)$input;
        }
        self::error(self::typeMismatchError('int', $input), $context);
        return null;
    }

    /**
     * Convert any value to float if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     * @param ?Context $context
     *   Context if available
     */
    public static function toFloat($input, ?Context $context = null): ?float
    {
        if (\is_float($input)) {
            return $input;
        }
        if (\is_int($input)) {
            return (float)$input;
        }
        if (\is_string($input)) {
            if (\ctype_digit($input)) {
                return (float)$input;
            }
            if (\preg_match('/^\d+\.\d+$/', $input)) {
                return (float)$input;
            }
        }
        self::error(self::typeMismatchError('float', $input), $context);
        return null;
    }
}

/**
 * Option type
 */
final class ValueOption
{
    /** @var bool */
    public $success = false;

    /** @var mixed */
    public $value;

    /**
     * Got a value
     */
    public static function ok($value): self
    {
        $ret = new self;
        $ret->value = $value;
        $ret->success = true;
        return $ret;
    }

    /**
     * Does not handle type
     */
    public static function miss(): self
    {
        return new self;
    }
}
