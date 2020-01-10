<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Normalization runtime helper.
 *
 * Defines commonly used code which will be used by normalizers at runtime.
 */
final class RuntimeHelper
{
    /**
     * Alias of \gettype() which returns PHP type hints.
     *
     * @param mixed $value
     *   Any value.
     *
     * @return string
     *   PHP native type name, or FQDN if input is an object.
     *   It may return 'array' as well.
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
        // https://www.php.net/manual/en/function.gettype.php - as of PHP 7.2.0.
        if ('resource (closed)' === $type) {
            return 'resource';
        }
        return $type;
    }

    /**
     * Are those classes in different namespaces.
     */
    public static function inSameNamespace(string $classA, string $classB): bool
    {
        return self::getClassNamespace($classA) === self::getClassNamespace($classB);
    }

    /**
     * Get class namespace
     */
    public static function getClassNamespace(string $className): ?string
    {
        $className = \trim($className, '\\');
        if (false !== ($pos = \strrpos($className, '\\'))) {
            return \substr($className, 0, $pos);
        }
        return null;
    }

    /**
     * Get class shortname
     */
    public static function getClassShortName(string $className): string
    {
        $className = \trim($className, '\\');
        if (false !== ($pos = \strrpos($className, '\\'))) {
            return \substr($className, $pos + 1);
        }
        return $className;
    }

    /**
     * Find a specific value in given array.
     *
     * @param mixed[] $input
     *   Arbitrary input values.
     * @param string[] $candidates
     *   Allowed names for the value, first one will be returned, error will be
     *   raised in case more than one matches the candidates.
     */
    public static function find(array $input, array $candidates, Context $context): ValueOption
    {
        $verbose = $context ? $context->isVerbose() : false;
        $found = $value = null;

        foreach ($candidates as $name) {
            if (\array_key_exists($name, $input)) {
                if ($found) {
                    $context->addError(\sprintf("Duplicate value found: '%s' was already found in '%s'", $found, $name));
                } else if ($verbose) {
                    $found = $name;
                    $value = $input[$name];
                } else {
                    return ValueOption::ok($input[$name]);
                }
            }
        }

        return $found ? ValueOption::ok($value) : ValueOption::miss();
    }

    /**
     * Convert any value to string if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     */
    public static function toString($input, Context $context): ?string
    {
        if (\is_string($input)) {
            return $input;
        } else if (\is_object($input) && \method_exists($input, '__toString')) {
            return $input->__toString();
        }
        $context->typeMismatchError('string', self::getType($input));
        return null;
    }

    /**
     * Convert any value to bool if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     */
    public static function toBool($input, Context $context): ?bool
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
        $context->typeMismatchError('bool', self::getType($input));
        return null;
    }

    /**
     * Convert any value to int if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     */
    public static function toInt($input, Context $context): ?int
    {
        if (\is_int($input)) {
            return $input;
        }
        if (\is_string($input) && \ctype_digit($input)) {
            return (int)$input;
        }
        // Float, but integer value.
        if (\is_float($input) && $input == ($cast = (int)$input)) {
            return $cast;
        }
        $context->typeMismatchError('int', self::getType($input));
        return null;
    }

    /**
     * Convert any value to float if possible
     *
     * @param mixed $input
     *   Arbitrary input value
     */
    public static function toFloat($input, Context $context): ?float
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
        $context->typeMismatchError('float', self::getType($input));
        return null;
    }

    /**
     * Convert any value to mutable date
     */
    public static function toDate($input, Context $context): ?\DateTime
    {
        if (null !== ($ret = self::toDateImmutable($input, $context))) {
            return \DateTime::createFromImmutable($ret);
        }
        return null;
    }

    /**
     * Convert any value to immutable date
     */
    public static function toDateImmutable($input, Context $context): ?\DateTimeImmutable
    {
        if ($input instanceof \DateTimeImmutable) {
            return $input;
        }
        if ($input instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($input);
        }

        $format = $context->getOption(Option::DATE_FORMAT, \DateTime::RFC3339);
        try {
            // Try with context format.
            return \DateTimeImmutable::createFromFormat($format, $input);
        } catch (\Throwable $e) {
            try {
                // Else attempt with no format at all and prey.
                return new \DateTimeImmutable($format);
            } catch (\Throwable $e) {
                $context->addError("Invalid date format");
            }
        }

        return null;
    }

    /**
     * Degradation of re-entring into the generator.
     *
     * @todo This is basically cheating in benchmarks.
     */
    public static function denormalizeScalar(string $type, $input, Context $context): ValueOption
    {
        switch ($type) {
            case 'bool':
                return ValueOption::ok(self::toBool($input, $context));
            case 'float':
                return ValueOption::ok(self::toFloat($input, $context));
            case 'int':
                return ValueOption::ok(self::toInt($input, $context));
            case 'null':
                return ValueOption::ok($input);
            case 'string':
                return ValueOption::ok(self::toString($input, $context));
            case \DateTime::class:
                return ValueOption::ok(self::toDate($input, $context));
            case \DateTimeInterface::class:
            case \DateTimeImmutable::class:
                return ValueOption::ok(self::toDateImmutable($input, $context));
            case Uuid::class:
            case UuidInterface::class:
                return ValueOption::ok(Uuid::fromString($input));
        }
        return ValueOption::miss();
    }

    /**
     * Degradation of re-entring into the generator.
     *
     * @todo This is basically cheating in benchmarks.
     */
    public static function normalizeScalar(string $type, $input, Context $context): ValueOption
    {
        switch ($type) {
            case 'bool':
                return ValueOption::ok((bool)$input);
            case 'float':
                return ValueOption::ok((float)$input);
            case 'int':
                return ValueOption::ok((int)$input);
            case 'null':
                return ValueOption::ok($input);
            case 'string':
                return ValueOption::ok(self::toString($input, $context));
            case \DateTime::class:
            case \DateTimeInterface::class:
            case \DateTimeImmutable::class:
                $format = $context->getOption(Option::DATE_FORMAT, \DateTime::RFC3339);
                return ValueOption::ok($input->format($format));
            case Uuid::class:
            case UuidInterface::class:
                return ValueOption::ok($input->__toString());
        }
        return ValueOption::miss();
    }
}
