<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Denormalization;

use MakinaCorpus\Normalizer\Benchmarks\WithContextTrait;
use MakinaCorpus\Normalizer\Benchmarks\WithFallbackNormalizerTrait;

final class FallbackDenormalizerBench extends AbstractDenormalizeBench
{
    use WithContextTrait;
    use WithFallbackNormalizerTrait;

    /**
     * {@inheritdoc}
     */
    protected function denormalize(string $type, array $input): void
    {
        $this->normalizer->denormalize($type, $input, $this->context->fresh());
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
