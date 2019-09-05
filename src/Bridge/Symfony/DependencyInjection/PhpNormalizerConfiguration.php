<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class PhpNormalizerConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        ($treeBuilder = new TreeBuilder('php_normalizer'))
            ->getRootNode()
            ->children()
                ->arrayNode('definition_files')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
