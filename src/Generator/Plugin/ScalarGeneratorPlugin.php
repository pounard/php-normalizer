<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Plugin;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\ServiceConfigurationError;

/**
 * Handles scalars.
 */
final class ScalarGeneratorPlugin implements GeneratorPlugin
{
    /**
     * {@inheritdoc}
     */
    public function supports(PropertyDefinition $property, Context $context): bool
    {
        switch ($property->getTypeName()) {
            // When generating normalization (model to norm) from an object
            // we do trust the incomming value type and just copy the value.
            case 'bool':
            case 'float':
            case 'int':
            case 'string':
                return true;

            default:
                return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizeCode(PropertyDefinition $property, Context $context, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());

        switch ($property->getTypeName()) {
            // When generating normalization (model to norm) from an object
            // we do trust the incomming value type and just copy the value.
            case 'bool':
            case 'float':
            case 'int':
            case 'string':
                return "({$type}){$input}";

            default:
                throw new ServiceConfigurationError();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateDenormalizeCode(PropertyDefinition $property, Context $context, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());

        switch ($property->getTypeName()) {
            // When generating normalization (model to norm) from an object
            // we do trust the incomming value type and just copy the value.
            case 'bool':
            case 'float':
            case 'int':
            case 'string':
                $methodName = "to".\ucfirst($type);
                return "Helper::{$methodName}({$input})";

            default:
                throw new ServiceConfigurationError();
        }
    }
}
