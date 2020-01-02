<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\DenormalizationPhp74;

use MakinaCorpus\Normalizer\Benchmarks\WithContextTrait;
use MakinaCorpus\Normalizer\Benchmarks\WithGeneratedNormalizerTrait;

final class GeneratedDenormalizerBench extends AbstractDenormalizeBench
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
        return "makinacorpus/php-serializer (generated)";
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
