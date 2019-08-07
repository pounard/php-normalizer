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
        $treeBuilder = new TreeBuilder('php_normalizer');
        /** @var \Symfony\Component\Config\Definition\Builder\NodeBuilder $rootNode  */
        $rootNode = $treeBuilder->getRootNode();

        /*
        $rootNode
            ->children()
                ->arrayNode('runner')
                    ->children()
                        ->enumNode('driver')
                            ->values(['doctrine'])
                            ->defaultNull()
                        ->end()
                        ->enumNode('metadata_cache')
                            ->values(['array', 'apcu'])
                            ->defaultNull()
                        ->end()
                        ->scalarNode('metadata_cache_prefix')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('query')
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('domain')
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('event_store')->defaultFalse()->end()
                        ->booleanNode('lock_service')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
         */

        return $treeBuilder;
    }
}
