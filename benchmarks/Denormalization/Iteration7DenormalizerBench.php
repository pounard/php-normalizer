<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Denormalization;

use MakinaCorpus\Normalizer\Benchmarks\WithContextTrait;
use MakinaCorpus\Normalizer\Benchmarks\WithGeneratedNormalizerTrait;

final class Iteration7DenormalizerBench extends AbstractDenormalizeBench
{
    use WithContextTrait;
    use WithGeneratedNormalizerTrait;

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
        return "makinacorpus/php-serializer (#7)";
    }

    /**
     * {@inheritdoc}
     */
    public function initSerializer(): void
    {
        $this->normalizer = $this->createNormalizer('Generated7');
        $this->context = $this->createContext();
    }
}
