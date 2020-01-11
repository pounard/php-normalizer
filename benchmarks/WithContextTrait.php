<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;

trait WithContextTrait
{
    /** @var \MakinaCorpus\Normalizer\Context */
    private $context;

    /** @var \MakinaCorpus\Normalizer\ContextFactory */
    private $contextFactory;

    private function getContextFactory(): ContextFactory
    {
        return $this->contextFactory ?? (
            $this->contextFactory = new ContextFactory(
                /*
                new CacheItemPoolTypeDefinitionMapCache(
                    Context::createDefaultTypeDefinitionMap(),
                    new PhpFilesAdapter('MakinaCorpusNormalizer', 0, __DIR__.'/cache', true)
                ),
                 */
            )
        );
    }

    /**
     * Create context.
     */
    private function createContext(array $options = []): Context
    {
        return $this->getContextFactory()->createContext($options);
    }
}
