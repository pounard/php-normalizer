<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Plugin;

use MakinaCorpus\Normalizer\GeneratorContext;
use MakinaCorpus\Normalizer\Option;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\ServiceConfigurationError;

/**
 * \DateTime* objects normalizer.
 */
final class DateTimeGeneratorPlugin implements GeneratorPlugin
{
    /**
     * {@inheritdoc}
     */
    public function supports(PropertyDefinition $property, GeneratorContext $context): bool
    {
        $type = $context->getNativeType($property->getTypeName());

        switch ($type) {
            case \DateTime::class:
            case \DateTimeImmutable::class:
            case \DateTimeInterface::class:
                return true;
            default:
                return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());

        switch ($type) {
            case \DateTime::class:
            case \DateTimeImmutable::class:
            case \DateTimeInterface::class:
                $format = \addslashes($context->getOption(Option::DATE_FORMAT, \DateTime::RFC3339));
                return "{$input}->format('{$format}')";
            default:
                throw new ServiceConfigurationError();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateDenormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());
        $helperClass = $context->addImport(RuntimeHelper::class);

        switch ($type) {
            case \DateTime::class:
                return "{$helperClass}::toDate({$input}, \$context)";
            case \DateTimeImmutable::class:
            case \DateTimeInterface::class:
                return "{$helperClass}::toDateImmutable({$input}, \$context)";
            default:
                throw new ServiceConfigurationError();
        }
    }
}
