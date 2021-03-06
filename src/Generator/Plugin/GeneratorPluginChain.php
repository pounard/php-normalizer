<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator\Plugin;

use MakinaCorpus\Normalizer\GeneratorContext;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\RuntimeHelper;
use MakinaCorpus\Normalizer\ServiceConfigurationError;

/**
 * Generator plugin chain: main entry point for custom generation code.
 */
final class GeneratorPluginChain implements GeneratorPlugin
{
    /** @var GeneratorPlugin[] */
    private $plugins = [];

    /**
     * Default constructor
     *
     * @param 
     */
    public function __construct(iterable $plugins = [], bool $setDefaults = true)
    {
        foreach ($plugins as $index => $plugin) {
            if (!$plugin instanceof GeneratorPlugin) {
                throw new ServiceConfigurationError(\sprintf(
                    "Expected an implementation of '%s' got '%s' at index %s",
                    GeneratorPlugin::class, RuntimeHelper::getType($plugin), $index
                ));
            }
            $this->plugins[] = $plugin;
        }

        // Add some defaults to run after the user given ones.
        if ($setDefaults) {
            $this->plugins[] = new ScalarGeneratorPlugin();
            $this->plugins[] = new DateTimeGeneratorPlugin();
            $this->plugins[] = new UuidGeneratorPlugin();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PropertyDefinition $property, GeneratorContext $context): bool
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->supports($property, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->supports($property, $context)) {
                return $plugin->generateNormalizeCode($property, $context, $input);
            }
        }
        // @codeCoverageIgnoreStart
        throw new ServiceConfigurationError(\sprintf(
            "%s::supports() was not called prior to calling %s::generateNormalizeCode()",
            __CLASS__, __CLASS__
        ));
        // @codeCoverageIgnoreStop
    }

    /**
     * {@inheritdoc}
     */
    public function generateDenormalizeCode(PropertyDefinition $property, GeneratorContext $context, string $input): string
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->supports($property, $context)) {
                return $plugin->generateDenormalizeCode($property, $context, $input);
            }
        }
        // @codeCoverageIgnoreStart
        throw new ServiceConfigurationError(\sprintf(
            "%s::supports() was not called prior to calling %s::generateDenormalizeCode()",
            __CLASS__, __CLASS__
        ));
        // @codeCoverageIgnoreStop
    }
}
