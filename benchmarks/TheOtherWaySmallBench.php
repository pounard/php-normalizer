<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

/**
 * Benchmark stream denormalization
 *
 * @BeforeMethods({"setUp"})
 */
final class TheOtherWaySmallBench
{
    use NormalizerBenchmarkTrait;

    /**
     * Use this method for benchmark setup
     */
    public function setUp(): void
    {
        $this->initializeComponents();
        $this->data = ObjectGenerator::createAndHydrateMessages(10);
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\normalizer1($data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not(\normalizer1($data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithReflection() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer7->normalize($data, $this->getContextWithReflection()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            display_or_not($this->normalizer7->normalize($data, $this->getContextWithConfigOnly()));
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->normalize(AddToCartMessage::class, $data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchCustomWithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->normalize(AddToCartMessage::class, $data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchSymfony() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizer->normalize($data);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchSymfonyProxy() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizerProxy->normalize($data);
        }
    }
}
