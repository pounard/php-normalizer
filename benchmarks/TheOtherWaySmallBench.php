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
    public function benchMap() : void
    {
        $context = $this->context->fresh();
        foreach ($this->data as $data) {
            $this->defaultNormalizer->normalize(AddToCartMessage::class, $data, $context);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(30)
     */
    public function benchReflection() : void
    {
        $context = $this->cachedContext->fresh();
        foreach ($this->data as $data) {
            $this->defaultNormalizer->normalize(AddToCartMessage::class, $data, $context);
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
