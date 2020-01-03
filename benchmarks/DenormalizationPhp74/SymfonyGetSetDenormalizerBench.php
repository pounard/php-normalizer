<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\DenormalizationPhp74;

use MakinaCorpus\Normalizer\Benchmarks\WithSymfonyGetSetNormalizerTrait;

final class SymfonyGetSetDenormalizerBench extends AbstractDenormalizeBench
{
    use WithSymfonyGetSetNormalizerTrait;

    /**
     * {@inheritdoc}
     */
    protected function denormalize(string $type, array $input): void
    {
        $this->serializer->denormalize($input, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageName(): string
    {
        return "symfony/serializer (object normalizer)";
    }

    /**
     * {@inheritdoc}
     */
    public function initSerializer(): void
    {
        $this->serializer = $this->createSymfonyNormalizer();
    }
}
