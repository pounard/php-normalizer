<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Normalization;

use MakinaCorpus\Normalizer\Benchmarks\WithSymfonyObjectNormalizerTrait;
use MakinaCorpus\Normalizer\Mock\MockArticle;

final class SymfonyObjectNormalizerBench extends AbstractNormalizeBench
{
    use WithSymfonyObjectNormalizerTrait;

    /**
     * {@inheritdoc}
     */
    protected function normalize(MockArticle $object): void
    {
        $this->display($this->serializer->normalize($object));
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageName(): string
    {
        return "symfony/serializer (objet normalizer)";
    }

    /**
     * {@inheritdoc}
     */
    public function initSerializer(): void
    {
        $this->serializer = $this->createSymfonyNormalizer();
    }
}
