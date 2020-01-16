<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Symfony;

use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\PhpNormalizerExtension;
use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\Compiler\GoatBridgePass;
use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\Compiler\NormalizerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
final class PhpNormalizerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NormalizerPass());
        $container->addCompilerPass(new GoatBridgePass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new PhpNormalizerExtension();
    }
}
