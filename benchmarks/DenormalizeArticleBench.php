<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

/**
 * Benchmark stream denormalization
 *
 * @BeforeMethods({"setUp"})
 */
class DenormalizeArticleBench extends AbstractNormalizerBenchmark
{
    public function setUp() : void
    {
        $this->createTypeDefinitionMap();
        $this->createDefaultNormalizer();
        $this->createContext();
        $this->createArticleData();
    }

    /**
     * @Revs(100)
     * @Iterations(30)
     */
    public function benchConsume() : void
    {
        foreach ($this->data as $data) {
            $this->defaultNormalizer->denormalize('app.article', $data, $this->context);
        }
    }
}
