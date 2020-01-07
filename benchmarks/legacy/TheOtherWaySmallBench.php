<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use function MakinaCorpus\Normalizer\Generator\Iterations\normalizer1;
use MakinaCorpus\Normalizer\Mock\ObjectGenerator;

/**
 * Benchmark stream denormalization
 *
 * Iteration 2 to 6 are disabled because they don't support class inheritance,
 * or wrongly, which makes results bias and useless.
 *
 * @BeforeMethods({"setUp"})
 */
final class TheOtherWaySmallBench
{
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
            normalizer1($data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration1WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            normalizer1($data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer7->normalize($data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration7WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer7->normalize($data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration8WithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer8->normalize($data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchIteration8WithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->normalizer8->normalize($data, $this->getContextWithConfigOnly());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchFallbackWithReflection() : void
    {
        foreach ($this->data as $data) {
            $this->fallbackNormalizer->normalize($data, $this->getContextWithReflection());
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchFallbackWithConfigOnly() : void
    {
        foreach ($this->data as $data) {
            $this->fallbackNormalizer->normalize($data, $this->getContextWithConfigOnly());
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
