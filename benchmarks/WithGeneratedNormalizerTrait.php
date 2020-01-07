<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\Generator\StaticMapRegistry;
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
    public function createNormalizer(string $namespace = 'Normalizer'): Normalizer
    {
        return new DefaultNormalizer(
            new StaticMapRegistry(
                \dirname(__DIR__).'/normalizers.php'
            ),
            new CustomNormalizerChain([
                new DateTimeNormalizer(),
                new UuidNormalizer()
            ]),
        );
    }
}
