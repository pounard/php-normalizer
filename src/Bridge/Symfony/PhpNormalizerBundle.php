<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Symfony;

use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\Compiler\NormalizerPass;
use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\PhpNormalizerExtension;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new PhpNormalizerExtension();
    }
}
