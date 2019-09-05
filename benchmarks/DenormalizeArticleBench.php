<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

/**
 * Benchmark stream denormalization
 *
 * @BeforeMethods({"setUp"})
 */
final class DenormalizeArticleBench
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
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator1(MockArticle::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1() : void
    {
        foreach ($this->data as $data) {
            \hydrator1(MockArticle::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator2(MockArticle::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2() : void
    {
        foreach ($this->data as $data) {
            \hydrator2(MockArticle::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration3WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator3(MockArticle::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration3() : void
    {
        foreach ($this->data as $data) {
            \hydrator3(MockArticle::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithConfig() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(MockArticle::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(MockArticle::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchSymfony() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizer->denormalize($data, MockArticle::class);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchSymfonyProxy() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizerProxy->denormalize($data, MockArticle::class);
        }
    }
}
