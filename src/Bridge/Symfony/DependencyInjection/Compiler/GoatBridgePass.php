<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\Compiler;

use MakinaCorpus\Normalizer\Bridge\Goat\NormalizerHydratorMap;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Registers custom normalizers and type definition maps
 */
final class GoatBridgePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('goat.hydrator_map')) {
            $container->addDefinitions([
                (new Definition())
                    ->setClass(NormalizerHydratorMap::class)
                    ->setArguments([
                        new Reference('php_normalizer'),
                        new Reference('goat.hydrator_map.inner'),
                    ])
                    ->setDecoratedService('goat.hydrator_map')
            ]);
        }
    }
}
