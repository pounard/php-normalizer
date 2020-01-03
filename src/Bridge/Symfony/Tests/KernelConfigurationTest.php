<?php

namespace MakinaCorpus\Normalizer\Bridge\Symfony\Tests;

use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\PhpNormalizerExtension;
use MakinaCorpus\Normalizer\Bridge\Symfony\DependencyInjection\Compiler\NormalizerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class KernelConfigurationTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\DependencyInjection\ContainerBuilder')) {
            self::markTestSkipped("This test can only run with symfony/dependency-injection component alongside");
        }
    }

    private function getContainer()
    {
        // Code inspired by the SncRedisBundle, all credits to its authors.
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug'=> false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => \sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => \dirname(__DIR__),
        ]));
    }

    private function getMinimalConfig(): array
    {
        return [
            'definition_files' => [
                __DIR__.'/Fixtures/definitions1.yaml',
                '%kernel.root_dir%/Tests/Fixtures/definitions2.yaml',
            ],
        ];
    }

    /**
     * Test default config for resulting tagged services
     */
    public function testTaggedServicesConfigLoad()
    {
        $extension = new PhpNormalizerExtension();
        $config = $this->getMinimalConfig();
        $extension->load([$config], $container = $this->getContainer());

        self::assertCount(3, $container->findTaggedServiceIds('php_normalizer.normalizer'));
        self::assertCount(1, $container->findTaggedServiceIds('serializer.normalizer'));
        self::assertCount(2, $container->findTaggedServiceIds('php_normalizer.type_definition_map'));
    }

    /**
     * Test normalizer pass executes
     */
    public function testNormalizerPass()
    {
        $extension = new PhpNormalizerExtension();
        $config = $this->getMinimalConfig();
        $extension->load([$config], $container = $this->getContainer());

        $container->getDefinition('php_normalizer.normalizer')->setPublic(true);
        $container->getDefinition('php_normalizer.type_definition_map')->setPublic(true);
        $container->addCompilerPass(new NormalizerPass());

        $container->compile();

        $definition = $container->getDefinition('php_normalizer.normalizer');
        self::assertCount(3, $definition->getArgument(0));

        $definition = $container->getDefinition('php_normalizer.type_definition_map');
        self::assertCount(2, $definition->getArgument(0));
    }
}
