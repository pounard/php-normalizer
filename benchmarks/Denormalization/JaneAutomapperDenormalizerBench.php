<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Denormalization;

use Jane\AutoMapper\Context;
use MakinaCorpus\Normalizer\Mock\MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\WithJaneAutomapperTrait;

final class JaneAutomapperDenormalizerBench extends AbstractDenormalizeBench
{
    use WithJaneAutomapperTrait;

    /**
     * {@inheritdoc}
     */
    protected function denormalize(string $type, array $input): void
    {
        $this->mapper->map($input, new Context());
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
        $this->mapper = $this->createDenormalizerMapper(MockArticle::class);
    }
}
