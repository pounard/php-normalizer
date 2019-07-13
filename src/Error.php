<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

interface NormalizerError
{
}

interface ConfigurationError extends NormalizerError
{
}

interface DataTransformationError extends NormalizerError
{
}

final class NotImplementedError
    extends \BadMethodCallException
    implements NormalizerError
{
}

final class CircularDependencyDetectedError
    extends \InvalidArgumentException
    implements DataTransformationError
{
}

final class InvalidValueTypeError
    extends \InvalidArgumentException
    implements DataTransformationError
{
}

final class UnupportedTypeError
    extends \InvalidArgumentException
    implements DataTransformationError
{
}

final class CouldNotFindTypeInfo
    extends \InvalidArgumentException
    implements ConfigurationError
{
}

final class InvalidOptionValueError
    extends \InvalidArgumentException
    implements ConfigurationError
{
}

final class TypeDoesNotExistError
    extends \InvalidArgumentException
    implements ConfigurationError
{
}
