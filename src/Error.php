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
