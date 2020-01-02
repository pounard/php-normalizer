<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\Generator\Generator;
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
            new class implements Generator
            {
                public function getNormalizerClass(string $className): ?string
                {
                    return null;
                }

                public function generateNormalizerClass(string $className): string
                {
                    throw new \Exception("Not implemeted.");
                }
            },
            new CustomNormalizerChain([
                new DateTimeNormalizer(),
                new UuidNormalizer()
            ])
        );
    }
}
