<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\NormalizerRegistry;
use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;
use MakinaCorpus\Normalizer\Normalizer\DateTimeNormalizer;
use MakinaCorpus\Normalizer\Normalizer\UuidNormalizer;

trait WithFallbackNormalizerTrait
{
    /** @var Normalizer */
    private $normalizer;

    /**
     * {@inheritdoc}
     */
    public function createNormalizer(): Normalizer
    {
        return new DefaultNormalizer(
            new class implements NormalizerRegistry
            {
                public function find(string $className): ?string
                {
                    return null;
                }
            },
            new CustomNormalizerChain([
                new DateTimeNormalizer(),
                new UuidNormalizer()
            ])
        );
    }
}
