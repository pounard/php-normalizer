<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Normalization;

use MakinaCorpus\Normalizer\Benchmarks\WithSymfonyGetSetNormalizerTrait;
use MakinaCorpus\Normalizer\Mock\MockArticle;

final class SymfonyObjectDenormalizerBench extends AbstractNormalizeBench
{
    use WithSymfonyGetSetNormalizerTrait;

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
        return "symfony/serializer (get-set normalizer)";
    }

    /**
     * {@inheritdoc}
     */
    public function initSerializer(): void
    {
        $this->serializer = $this->createSymfonyNormalizer();
    }
}
