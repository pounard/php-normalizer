<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests;

use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\Generator\DefaultGenerator;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Generator\StaticMapRegistry;
use MakinaCorpus\Normalizer\Mock\AddToCartMessage as AddToCartMessageBench;
use MakinaCorpus\Normalizer\Mock\MockArticle as MockArticleBench;
use MakinaCorpus\Normalizer\Mock\MockTextWithFormat as MockTextWithFormatBench;
use MakinaCorpus\Normalizer\Mock\MockWithText as MockWithTextBench;
use MakinaCorpus\Normalizer\Mock\MockWithTitle as MockWithTitleBench;
use MakinaCorpus\Normalizer\Mock\Php74AddToCartMessage;
use MakinaCorpus\Normalizer\Mock\Php74MockArticle;
use MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat;
use MakinaCorpus\Normalizer\Mock\Php74MockWithText;
use MakinaCorpus\Normalizer\Mock\Php74MockWithTitle;
use PHPUnit\Framework\TestCase;

/**
 * This is not a test, this just generate files.
 *
 * I needed a way to do it quickly.
 */
final class StupidTest extends TestCase
{
    public static function dataClassName()
    {
        // Benchmarks
        $basedir = \dirname(__DIR__).'/mock';
        yield [AddToCartMessageBench::class, $basedir];
        yield [MockArticleBench::class, $basedir];
        yield [MockTextWithFormatBench::class, $basedir];
        yield [MockWithTextBench::class, $basedir];
        yield [MockWithTitleBench::class, $basedir];
        yield [Php74AddToCartMessage::class, $basedir];
        yield [Php74MockArticle::class, $basedir];
        yield [Php74MockTextWithFormat::class, $basedir];
        yield [Php74MockWithText::class, $basedir];
        yield [Php74MockWithTitle::class, $basedir];
    }

    /**
     * @dataProvider dataClassName
     */
    public function testNormalizerDefaultGenerator(string $className, string $basedir)
    {
        self::expectNotToPerformAssertions();

        $generator = new DefaultGenerator(
            new ContextFactory(),
            $basedir,
            new StaticMapRegistry(\dirname(__DIR__).'/mock/Generated/registry.php'),
            'MakinaCorpus\Normalizer\Mock',
            new Psr4AppNamingStrategy(
                'Normalizer',
                'Generated',
                'MakinaCorpus\Normalizer\Mock'
            )
        );
        $generator->generateNormalizerClass($className);
    }
}
