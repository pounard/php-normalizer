<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\DenormalizationPhp74;

use MakinaCorpus\Normalizer\Mock\Php74MockArticle;
use MakinaCorpus\Normalizer\Mock\Php74ObjectGenerator;

/**
 * @BeforeMethods({"initSerializer", "initData"})
 * @Warmup(1)
 * @Revs(5)
 * @Iterations(5)
 */
abstract class AbstractDenormalizeBench
{
    /** @var mixed[] */
    private $data;

    /**
     * Generate data.
     */
    final public function initData(array $params): void
    {
        $this->data = Php74ObjectGenerator::createNormalizedArticleList(5, $this->withUuid());
    }

    /**
     * Bench.
     */
    final public function benchSerialize(): void
    {
        foreach ($this->data as $input) {
            $this->denormalize(Php74MockArticle::class, $input);
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
    abstract protected function denormalize(string $type, array $input): void;

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
