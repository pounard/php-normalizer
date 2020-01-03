<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

interface NormalizerError
{
}

interface ConfigurationError extends NormalizerError
{
}

class ServiceConfigurationError
    extends \LogicException
    implements ConfigurationError
{
}

interface DataTransformationError extends NormalizerError
{
}

class RuntimeError
    extends \InvalidArgumentException
    implements DataTransformationError
{
}

final class NotImplementedError
    extends \BadMethodCallException
    implements NormalizerError
{
}

final class CircularDependencyDetectedError
    extends RuntimeError
    implements DataTransformationError
{
}

final class UnsupportedTypeError
    extends RuntimeError
    implements DataTransformationError
{
    public function __construct($type, $code = 0, $previous = null)
    {
        if (\strpos($type, ' ')) {
            $message = $type;
        } else {
            $message = \sprintf("Unsupported type '%s'", $type);
        }
        parent::__construct($message, (int)$code, $previous);
    }
}

final class CouldNotFindTypeInfo
    extends RuntimeError
    implements ConfigurationError
{
}

final class InvalidOptionValueError
    extends RuntimeError
    implements ConfigurationError
{
}

class TypeMismatchError
    extends RuntimeError
    implements ConfigurationError
{
    public function __construct($expected, $type = null, $code = 0, $previous = null)
    {
        if (null === $type) {
            $message = $expected;
        } else {
            $message = \sprintf("Expected type '%s', got '%s'", $expected, $type);
        }
        parent::__construct($message, (int)$code, $previous);
    }
}

final class InvalidValueTypeError
    extends TypeMismatchError
{
}

class TypeDoesNotExistError
    extends RuntimeError
    implements ConfigurationError
{
    public function __construct($type, $code = 0, $previous = null)
    {
        if (\strpos($type, ' ')) {
            $message = $type;
        } else {
            $message = \sprintf("Could not find type '%s'", $type);
        }
        parent::__construct($message, (int)$code, $previous);
    }
}

class ClassDoesNotExistError
    extends TypeDoesNotExistError
{
    public function __construct($type, $code = 0, $previous = null)
    {
        if (\strpos($type, ' ')) {
            $message = $type;
        } else {
            $message = \sprintf("Class does not exists '%s'", $type);
        }
        parent::__construct($message, (int)$code, $previous);
    }
}
