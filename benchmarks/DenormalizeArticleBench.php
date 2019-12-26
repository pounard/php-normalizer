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
    public function benchIteration1WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator1(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator1(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator2(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator2(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     *
    public function benchIteration3WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator3(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }
     */

    /**
     * @Revs(50)
     * @Iterations(30)
     *
    public function benchIteration3WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator3(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }
     */

    /**
     * @Revs(50)
     * @Iterations(30)
     *
    public function benchIteration4WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator4(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }
     */

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration4WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\hydrator4(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration5WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer5->denormalize(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration5WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer5->denormalize(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration6WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer6->denormalize(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration6WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer6->denormalize(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer7->denormalize(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer7->denormalize(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration8WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer8->denormalize(MockArticle::class, $data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration8WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer8->denormalize(MockArticle::class, $data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(MockArticle::class, $data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(MockArticle::class, $data, $this->getContextWithConfigOnly());
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
