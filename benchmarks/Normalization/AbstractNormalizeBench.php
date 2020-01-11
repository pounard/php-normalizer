<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Normalization;

use MakinaCorpus\Normalizer\Benchmarks\WithStupidDisplayTrait;
use MakinaCorpus\Normalizer\Mock\MockArticle;
use MakinaCorpus\Normalizer\Mock\ObjectGenerator;

/**
 * @BeforeMethods({"initSerializer", "initData"})
 * @Warmup(1)
 * @Revs(5)
 * @Iterations(5)
 */
abstract class AbstractNormalizeBench
{
    use WithStupidDisplayTrait;

    /** @var mixed[] */
    private $data;

    /**
     * Generate data.
     */
    final public function initData(array $params): void
    {
        $this->data = ObjectGenerator::createInstanceArticleList(5, $this->withUuid());
    }

    /**
     * Bench.
     */
    final public function benchSerialize(): void
    {
        foreach ($this->data as $input) {
            $this->normalize($input);
        }
    }

    /**
     * Sadly, lots of normalizers can't handle them.
     *
     * Please note that returning false here is seriously with benchmarks cheating.
     */
    protected function withUuid(): bool
    {
        return false;
    }

    /**
     * Initialize your API.
     */
    abstract public function initSerializer(): void;

    /**
     * Denormalize given data.
     */
    abstract protected function normalize(MockArticle $object): void;

    /**
     * Get package name.
     */
    abstract public function getPackageName(): string;

    /**
     * Get note.
     */
    public function getNote(): ?string
    {
        return null;
    }
}
