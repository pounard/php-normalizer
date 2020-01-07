<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests;

use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\Benchmarks\AddToCartMessage as AddToCartMessageBench;
use MakinaCorpus\Normalizer\Benchmarks\MockArticle as MockArticleBench;
use MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat as MockTextWithFormatBench;
use MakinaCorpus\Normalizer\Benchmarks\MockWithText as MockWithTextBench;
use MakinaCorpus\Normalizer\Benchmarks\MockWithTitle as MockWithTitleBench;
use MakinaCorpus\Normalizer\Benchmarks\Php74AddToCartMessage;
use MakinaCorpus\Normalizer\Benchmarks\Php74MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\Php74MockTextWithFormat;
use MakinaCorpus\Normalizer\Benchmarks\Php74MockWithText;
use MakinaCorpus\Normalizer\Benchmarks\Php74MockWithTitle;
use MakinaCorpus\Normalizer\Generator\DefaultGenerator;
use MakinaCorpus\Normalizer\Generator\StaticMapRegistry;
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
        $basedir = \dirname(__DIR__).'/benchmarks';
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
            new StaticMapRegistry(\dirname(__DIR__).'/normalizers.php')
        );
        $generator->generateNormalizerClass($className);
    }
}
