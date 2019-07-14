<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

/**
 * Benchmark stream denormalization
 *
 * @BeforeMethods({"setUp"})
 */
class DenormalizeArticleBench
{
    use NormalizerBenchmarkTrait;

    private $data;

    /**
     * Use this method for benchmark setup
     */
    public function setUp(): void
    {
        $this->initializeComponents();
        $this->data = ObjectGenerator::createArticles(10);
    }

    /**
     * @Revs(100)
     * @Iterations(30)
     */
    public function benchMap() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(MockArticle::class, $data, $this->context);
        }
    }

    /**
     * @Revs(100)
     * @Iterations(30)
     */
    public function benchReflection() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(MockArticle::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(100)
     * @Iterations(30)
     */
    public function benchSymfony() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizer->denormalize($data, MockArticle::class);
        }
    }
}
