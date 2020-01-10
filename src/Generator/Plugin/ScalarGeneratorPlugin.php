<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Plugin;

use MakinaCorpus\Normalizer\GeneratorContext;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\ServiceConfigurationError;

/**
 * Handles scalars.
 */
final class ScalarGeneratorPlugin implements GeneratorPlugin
{
    /**
     * {@inheritdoc}
     */
    public function supports(PropertyDefinition $property, GeneratorContext $context): bool
    {
        $type = $context->getNativeType($property->getTypeName());

        switch ($type) {
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
    public function generateNormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());

        switch ($type) {
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
    public function generateDenormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        $type = $context->getNativeType($property->getTypeName());
        $helperClass = $context->addImport(RuntimeHelper::class);

        switch ($type) {
            case 'bool':
                return "{$helperClass}::toBool({$input}, \$context)";
            case 'float':
                return "{$helperClass}::toFloat({$input}, \$context)";
            case 'int':
                return "{$helperClass}::toInt({$input}, \$context)";
            case 'string':
                return "{$helperClass}::toString({$input}, \$context)";
            default:
                throw new ServiceConfigurationError();
        }
    }
}
