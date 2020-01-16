<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PhpNormalizerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('core.yaml');

        $this->registerDefinitionFiles($container, $config);
    }

    /**
     * Register custom application-defined type configuration files.
     */
    private function registerDefinitionFiles(ContainerBuilder $container, array $config): void
    {
        if (empty($config['definition_files'])) {
            return;
        }

        foreach ($config['definition_files'] as $filename) {
            $filename = $container->resolveEnvPlaceholders($filename, true);

            if (!\file_exists($filename)) {
                throw new InvalidArgumentException(\sprintf('File "%s" does not exist', $filename));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new PhpNormalizerConfiguration();
    }
}
