<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Plugin;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\PropertyDefinition;

/**
 * Generator plugin allows for generating alternative code for object
 * (de)normalization.
 *
 * All code generated by plugins must be correctly indented if you wish the
 * result to look good. Do not add extra indentation, main generator will add
 * any if necessary.
 */
interface GeneratorPlugin
{
    /**
     * Does this generator handles the given property.
     */
    public function supports(PropertyDefinition $property, Context $context): bool;

    /**
     * Generate normalize code.
     *
     * This code will always be called with a value as input, which will never
     * be a null value.
     *
     * @param string $input
     *   Name of variable this code must normalize. It can be anything such as
     *   \$input, \$input['foo'] or $input->foo, so please do not escape it,
     *   do not attempt anything that would break in any of those three use
     *   cases.
     */
    public function generateNormalizeCode(PropertyDefinition $property, Context $context, string $input): string;

    /**
     * Generate denormalize code.
     *
     * This code will always be called with a value as input, which will never
     * be a null value.
     *
     * @param string $inputValueName
     *   Name of variable this code must normalize. It can be anything such as
     *   \$input, \$input['foo'] or $input->foo, so please do not escape it,
     *   do not attempt anything that would break in any of those three use
     *   cases.
     */
    public function generateDenormalizeCode(PropertyDefinition $property, Context $context, string $input): string;
}
