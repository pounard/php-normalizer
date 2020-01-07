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
        yield [AddToCartMessageBench::class];
        yield [MockArticleBench::class];
        yield [MockTextWithFormatBench::class];
        yield [MockWithTextBench::class];
        yield [MockWithTitleBench::class];
        yield [Php74AddToCartMessage::class];
        yield [Php74MockArticle::class];
        yield [Php74MockTextWithFormat::class];
        yield [Php74MockWithText::class];
        yield [Php74MockWithTitle::class];
    }

    /**
     * @dataProvider dataClassName
     */
    public function testNormalizerDefaultGenerator(string $className)
    {
        self::expectNotToPerformAssertions();

        $generator = new DefaultGenerator(
            new ContextFactory(),
            \dirname(__DIR__).'/mock',
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
