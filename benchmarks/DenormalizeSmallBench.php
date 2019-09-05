<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

if (!\function_exists('display_or_not')) {
    function display_or_not($values)
    {
        // print_r($values);
    }
}

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
            \hydrator1(AddToCartMessage::class, $data, $this->getContextWithCache());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1() : void
    {
        foreach ($this->data as $data) {
            \hydrator1(AddToCartMessage::class, $data, $this->getContextWithConfig());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator2(AddToCartMessage::class, $data, $this->getContextWithCache());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration2() : void
    {
        foreach ($this->data as $data) {
            \hydrator2(AddToCartMessage::class, $data, $this->getContextWithConfig());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration3WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator3(AddToCartMessage::class, $data, $this->getContextWithCache());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration3() : void
    {
        foreach ($this->data as $data) {
            \hydrator3(AddToCartMessage::class, $data, $this->getContextWithConfig());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration4WithCache() : void
    {
        foreach ($this->data as $data) {
            \hydrator4(AddToCartMessage::class, $data, $this->getContextWithCache());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration4() : void
    {
        foreach ($this->data as $data) {
            \hydrator4(AddToCartMessage::class, $data, $this->getContextWithConfig());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithConfig() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(AddToCartMessage::class, $data, $this->getContextWithConfig());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize(AddToCartMessage::class, $data, $this->getContextWithCache());
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
