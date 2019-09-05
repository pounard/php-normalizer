<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\DateNormalizer;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\MemoryTypeDefinitionMapCache;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\ScalarNormalizer;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage as AddToCartMessageBench;
use MakinaCorpus\Normalizer\Benchmarks\MockArticle as MockArticleBench;
use MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat as MockTextWithFormatBench;
use MakinaCorpus\Normalizer\Benchmarks\MockWithText as MockWithTextBench;
use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle as MockWithTitleBench;
use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\UuidNormalizer;
use MakinaCorpus\Normalizer\Tests\Functional\MockArticle;
use MakinaCorpus\Normalizer\Tests\Functional\MockTextWithFormat;
use MakinaCorpus\Normalizer\Tests\Functional\MockWithText;
use PHPUnit\Framework\TestCase;
use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use Symfony\Component\Yaml\Yaml;

final class StupidTest extends TestCase
{
    private function createTypeDefinitionMapForTests(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/Functional/definitions.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    private function createTypeDefinitionMapForBench(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(\dirname(__DIR__).'/benchmarks/definitions.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    /**
     * Create cached type definitions
     */
    private function createCachedTypeDefinitionMap(): TypeDefinitionMap
    {
        return new MemoryTypeDefinitionMapCache([
            $this->createTypeDefinitionMapForBench(),
            $this->createTypeDefinitionMapForTests(),
            new ReflectionTypeDefinitionMap()
        ]);
    }

    /**
     * Create default normalizer
     */
    private function createDefaultNormalizer(): DefaultNormalizer
    {
        return $this->defaultNormalizer = new DefaultNormalizer([
            new ScalarNormalizer(),
            new DateNormalizer(),
            new UuidNormalizer(),
        ]);
    }

    /**
     * Create context
     */
    private function createCachedContext(array $options = []): Context
    {
        return $this->cachedContext = new Context($this->createCachedTypeDefinitionMap(), $options);
    }

    /**
     * Data provider
     */
    private static function dataClassName(int $iteration)
    {
        // Functional tests
        $basedir = __DIR__.'/Generated'.$iteration;
        yield [MockArticle::class, $basedir];
        yield [MockTextWithFormat::class, $basedir];
        yield [MockWithText::class, $basedir];

        // Benchmarks
        $basedir = \dirname(__DIR__).'/benchmarks/Generated'.$iteration;
        yield [AddToCartMessageBench::class, $basedir];
        yield [MockArticleBench::class, $basedir];
        yield [MockTextWithFormatBench::class, $basedir];
        yield [MockWithTextBench::class, $basedir];
        yield [MockWithTitleBench::class, $basedir];
    }

    public static function dataClassName2()
    {
        yield from self::dataClassName(2);
    }

    public static function dataClassName4()
    {
        yield from self::dataClassName(4);
    }

    /**
     * @dataProvider dataClassName2
     */
    public function testNormalizerGeneration2(string $className, string $basedir)
    {
        $filename = $basedir.'/'.\str_replace('\\', '/', \ltrim($className, '\\')).'Normalizer.php';
        $directory = \dirname($filename);

        if (!\is_dir($directory) && !@\mkdir($directory, 0750, true)) {
            throw new \RuntimeException(\sprintf("%s: could not create directory", $directory));
        }
        if (!\is_writable($directory)) {
            throw new \RuntimeException(\sprintf("%s: directory is not writable", $directory));
        }

        $writer = new \Writer($filename);
        $context = new Context($this->createCachedTypeDefinitionMap());
        \generate2_denormalizer_class($className, $context, $writer);
    }

    /**
     * @dataProvider dataClassName4
     */
    public function testNormalizerGeneration4(string $className, string $basedir)
    {
        $filename = $basedir.'/'.\str_replace('\\', '/', \ltrim($className, '\\')).'Normalizer.php';
        $directory = \dirname($filename);

        if (!\is_dir($directory) && !@\mkdir($directory, 0750, true)) {
            throw new \RuntimeException(\sprintf("%s: could not create directory", $directory));
        }
        if (!\is_writable($directory)) {
            throw new \RuntimeException(\sprintf("%s: directory is not writable", $directory));
        }

        $writer = new \Writer($filename);
        $context = new Context($this->createCachedTypeDefinitionMap());
        \generate4_denormalizer_class($className, $context, $writer);
    }
}