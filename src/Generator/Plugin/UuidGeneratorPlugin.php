<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Plugin;

use MakinaCorpus\Normalizer\GeneratorContext;
use MakinaCorpus\Normalizer\PropertyDefinition;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * ramsey/uuid objects normalizer.
 */
final class UuidGeneratorPlugin implements GeneratorPlugin
{
    /**
     * {@inheritdoc}
     */
    public function supports(PropertyDefinition $property, GeneratorContext $context): bool
    {
        $type = $context->getNativeType($property->getTypeName());

        return $type === UuidInterface::class || $type === Uuid::class;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        return "{$input}->__toString()";
    }

    /**
     * {@inheritdoc}
     */
    public function generateDenormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        $localClassName = $context->addImport(Uuid::class);

        return $localClassName."::fromString({$input})";
    }
}
