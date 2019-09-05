<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

/**
 * Benchmark stream denormalization
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
    public function benchIteration1WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator1(AddToCartMessage::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1() : void
    {
        foreach ($this->data as $data) {
            \hydrator1(AddToCartMessage::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator2(AddToCartMessage::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2() : void
    {
        foreach ($this->data as $data) {
            \hydrator2(AddToCartMessage::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration3WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator3(AddToCartMessage::class, $data, $this->cachedContext);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration3() : void
    {
        foreach ($this->data as $data) {
            \hydrator3(AddToCartMessage::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithConfig() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(AddToCartMessage::class, $data, $this->context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(AddToCartMessage::class, $data, $this->cachedContext);
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
