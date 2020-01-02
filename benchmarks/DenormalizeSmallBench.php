<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use function MakinaCorpus\Normalizer\Generator\Iterations\hydrator1;

/**
 * Benchmark stream denormalization
 *
 * Iteration 2 to 6 are disabled because they don't support class inheritance,
 * or wrongly, which makes results bias and useless.
 *
 * @BeforeMethods({"setUp"})
 */
final class DenormalizeSmallBench
{
    use NormalizerBenchmarkTrait;

    /**
     * Use this method for benchmark setup
     */
    public function setUp(): void
    {
        $this->initializeComponents();
        $this->data = ObjectGenerator::createMessages(10);
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithReflection() : void
    {
        foreach ($this->data as $data) {
            hydrator1(AddToCartMessage::class, $data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            hydrator1(AddToCartMessage::class, $data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer7->denormalize(AddToCartMessage::class, $data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer7->denormalize(AddToCartMessage::class, $data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration8WithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer8->denormalize(AddToCartMessage::class, $data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration8WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer8->denormalize(AddToCartMessage::class, $data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchFallbackWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->fallbackNormalizer->denormalize(AddToCartMessage::class, $data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchFallbackWithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->fallbackNormalizer->denormalize(AddToCartMessage::class, $data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchSymfony() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizer->denormalize($data, AddToCartMessage::class);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchSymfonyProxy() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizerProxy->denormalize($data, AddToCartMessage::class);
        }
    }
}
