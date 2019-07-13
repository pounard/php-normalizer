<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

/**
 * Benchmark stream denormalization with Symfony
 *
 * @BeforeMethods({"setUp"})
 */
class DenormalizeSmallSymfonyBench
{
    use SymfonyBenchmarkTrait;

    /**
     * @Revs(100)
     * @Iterations(30)
     */
    public function benchConsume() : void
    {
        foreach ($this->data as $data) {
            $this->symfonyNormalizer->denormalize($data, AddToCartMessage::class);
        }
    }
}

