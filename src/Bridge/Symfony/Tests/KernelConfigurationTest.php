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
            $this->markTestSkipped("This test can only run with symfony/dependency-injection component alongside");
        }
    }

    private function getContainer()
    {
        // Code inspired by the SncRedisBundle, all credits to its authors.
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug'=> false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__ . '/../../',
        ]));
    }

    /**
     * Test default config for resulting tagged services
     */
    public function testTaggedServicesConfigLoad()
    {
        $extension = new PhpNormalizerExtension();
        // $config = $this->parseYaml($this->getMinimalYamlConfig());
        $config = [];
        $extension->load([$config], $container = $this->getContainer());

        $this->assertCount(3, $container->findTaggedServiceIds('php_normalizer.normalizer'));
        $this->assertCount(1, $container->findTaggedServiceIds('serializer.normalizer'));
        $this->assertCount(2, $container->findTaggedServiceIds('php_normalizer.type_definition_map'));
    }

    /**
     * Test normalizer pass executes
     */
    public function testNormalizerPass()
    {
        $extension = new PhpNormalizerExtension();
        // $config = $this->parseYaml($this->getMinimalYamlConfig());
        $config = [];
        $extension->load([$config], $container = $this->getContainer());

        $container->getDefinition('php_normalizer.normalizer')->setPublic(true);
        $container->getDefinition('php_normalizer.type_definition_map')->setPublic(true);
        $container->addCompilerPass(new NormalizerPass());

        $container->compile();

        $definition = $container->getDefinition('php_normalizer.normalizer');
        $this->assertCount(3, $definition->getArgument(0));

        $definition = $container->getDefinition('php_normalizer.type_definition_map');
        $this->assertCount(2, $definition->getArgument(0));
    }
}
