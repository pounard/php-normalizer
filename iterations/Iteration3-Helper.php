<?php
/**
 * Iteration #3.
 *
 * Set of common functionnal helpers for both generated code and hydrator.
 *
 * Most conversion and validate functions can be used for both normalization
 * and denormalization. But normalization should work on valid entity objects
 * so validation will not be applied upon it.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Iterations;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Helper;

/**
 * Option type for hydrator1_external_implementation()
 */
final class HydratorOption
{
    /** @var bool */
    public $handled;

    /** @var mixed */
    public $value;

    /**
     * Got a value
     */
    public static function ok($value): self
    {
        $ret = new self;
        $ret->value = $value;
        $ret->handled = true;
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
function handle_error(string $message, ?Context $context = null): void
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
function find_value(array $input, array $candidates, ?Context $context = null)
{
    $verbose = $context ? $context->isVerbose() : false;
    $found = $value = null;
    foreach ($candidates as $name) {
        if (\array_key_exists($name, $input)) {
            if ($found) {
                handle_error(\sprintf("Value found '%s' but was already found in '%s'", $found, $name), $context);
            } else if ($verbose) {
                $found = $name;
                $value = $input[$name];
            } else {
                return $input[$name];
            }
        }
    }
    return $value;
}

/**
 * Validate scalar
 *
 * @param string $type
 * @param mixed $input
 *   Arbitrary input value
 * @param ?Context $context
 *   Context if available
 *
 * @return bool
 */
function validate_scalar(string $type, $input, ?Context $context = null): bool
{
    if ($type !== ($real = Helper::getType($input))) {
        handle_error(\sprintf("Expected value type '%s', got '%s'", $type, $real));
        return false;
    }
    return true;
}

/**
 * Validate object class
 *
 * @param string $type
 * @param mixed $input
 *   Arbitrary input value
 * @param ?Context $context
 *   Context if available
 *
 * @return bool
 */
function validate_object(string $type, object $input, ?Context $context = null): bool
{
    if ($type !== \get_class($input)) {
        handle_error(\sprintf("Expected value class '%s', got '%s'", $type, \get_class($input)));
        return false;
    }
    return true;
}

/**
 * Convert any value to string if possible
 *
 * @param mixed $input
 *   Arbitrary input value
 * @param ?Context $context
 *   Context if available
 */
function to_string($input, ?Context $context = null): string
{
    if (\is_string($input)) {
        return $input;
    } else if (\is_object($input) && \method_exists($input, '__toString()')) {
        return $input->__toString();
    }
    handle_error("Value is not a string", $context);
}

/**
 * Convert any value to bool if possible
 *
 * @param mixed $input
 *   Arbitrary input value
 * @param ?Context $context
 *   Context if available
 */
function to_bool($input, ?Context $context = null)
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
    handle_error("Value is not a boolean", $context);
}

/**
 * Convert any value to int if possible
 *
 * @param mixed $input
 *   Arbitrary input value
 * @param ?Context $context
 *   Context if available
 */
function to_int($input, ?Context $context = null)
{
    if (\is_int($input)) {
        return $input;
    }
    if (\is_string($input) && \ctype_digit($input)) {
        return (int)$input;
    }
    handle_error("Value is not an integer", $context);
}

/**
 * Convert any value to float if possible
 *
 * @param mixed $input
 *   Arbitrary input value
 * @param ?Context $context
 *   Context if available
 */
function to_float($input, ?Context $context = null)
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
    handle_error("Value is not an integer", $context);
}
