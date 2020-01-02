<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\Generator\GeneratorRuntime;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;
use MakinaCorpus\Normalizer\Normalizer\DateTimeNormalizer;
use MakinaCorpus\Normalizer\Normalizer\UuidNormalizer;

trait WithGeneratedNormalizerTrait
{
    /** @var Normalizer */
    private $normalizer;

    /**
     * {@inheritdoc}
     */
    public function createNormalizer(string $namespace = 'Generated8'): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', $namespace)
            ),
            new CustomNormalizerChain([
                new DateTimeNormalizer(),
                new UuidNormalizer()
            ]),
        );
    }
}
