<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Normalization;

use MakinaCorpus\Normalizer\Benchmarks\WithContextTrait;
use MakinaCorpus\Normalizer\Benchmarks\WithFallbackNormalizerTrait;
use MakinaCorpus\Normalizer\Mock\MockArticle;

final class FallbackNormalizerBench extends AbstractNormalizeBench
{
    use WithContextTrait;
    use WithFallbackNormalizerTrait;

    /**
     * {@inheritdoc}
     */
    protected function normalize(MockArticle $object): void
    {
        $this->display($this->normalizer->normalize($object, $this->context->fresh()));
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageName(): string
    {
        return "makinacorpus/php-serializer (fallback)";
    }

    /**
     * {@inheritdoc}
     */
    public function initSerializer(): void
    {
        $this->normalizer = $this->createNormalizer();
        $this->context = $this->createContext();
    }
}
