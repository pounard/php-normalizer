<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;

/**
 * Registers custom normalizers and type definition maps
 */
class NormalizerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private $normalizerChainService;
    private $normalizerTag;
    private $typeDefinitionMapService;
    private $typeDefinitionMapTag;

    /**
     * Default constructor
     */
    public function __construct(
        string $normalizerChainService = 'php_normalizer',
        string $normalizerTag = 'php_normalizer.normalizer',
        string $typeDefinitionMapService = 'php_normalizer.type_definition_map',
        string $typeDefinitionMapTag = 'php_normalizer.type_definition_map')
    {
        $this->normalizerChainService = $normalizerChainService;
        $this->normalizerTag = $normalizerTag;
        $this->typeDefinitionMapService = $typeDefinitionMapService;
        $this->typeDefinitionMapTag = $typeDefinitionMapTag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition($this->normalizerChainService)) {
            if ($normalizers = $this->findAndSortTaggedServices($this->normalizerTag, $container)) {
                $container
                    ->getDefinition($this->normalizerChainService)
                    ->replaceArgument(1, $normalizers)
                ;
            }
        }

        if ($container->hasDefinition($this->typeDefinitionMapService)) {
            if ($normalizers = $this->findAndSortTaggedServices($this->typeDefinitionMapTag, $container)) {
                $container
                    ->getDefinition($this->typeDefinitionMapService)
                    ->replaceArgument(0, $normalizers)
                ;
            }
        }
    }
}
