<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\NormalizerProxy;
use MakinaCorpus\Normalizer\Generator\GeneratorRuntime;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;
use MakinaCorpus\Normalizer\Normalizer\DateTimeNormalizer as CustomDateTimeNormalizer;
use MakinaCorpus\Normalizer\Normalizer\UuidNormalizer as CustomUuidNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Keeping this code for later, might become handy.
 *
 * @deprecated
 * @internal
 */
trait NormalizerBenchmarkTrait
{
    /**
     * Create iteration 6 normalizer
     */
    private function createNormalizer6(): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated5')
            ),
            new CustomNormalizerChain([
                new CustomDateTimeNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

    /**
     * Create iteration 7 normalizer
     */
    private function createNormalizer7(): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated7')
            ),
            new CustomNormalizerChain([
                new CustomDateTimeNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

    /**
     * Create iteration 8 normalizer
     */
    private function createNormalizer8(): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated8')
            ),
            new CustomNormalizerChain([
                new CustomDateTimeNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

    private function createSymfonyProxy()
    {
        return new Serializer([
            new NormalizerProxy(
                new ContextFactory(
                    $this->createCachedTypeDefinitionMap()
                ),
                $this->fallbackNormalizer
            ),
        ]);
    }
}
