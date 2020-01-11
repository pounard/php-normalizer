<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Normalization;

use Jane\AutoMapper\Context;
use MakinaCorpus\Normalizer\Mock\MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\WithJaneAutomapperTrait;

final class JaneAutomapperNormalizerBench extends AbstractNormalizeBench
{
    use WithJaneAutomapperTrait;

    /**
     * {@inheritdoc}
     */
    protected function normalize(MockArticle $object): void
    {
        $this->display($this->mapper->map($object, new Context()));
    }

    /**
     * {@inheritdoc}
     */
    protected function withUuid(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageName(): string
    {
        return "jane-php/automapper";
    }

    /**
     * {@inheritdoc}
     */
    public function initSerializer(): void
    {
        $this->mapper = $this->createMapper(MockArticle::class);
    }
}
