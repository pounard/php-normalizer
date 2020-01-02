<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Context;

trait WithContextTrait
{
    /** @var \MakinaCorpus\Normalizer\Context */
    private $context;

    /**
     * Create context.
     */
    private function createContext(array $options = []): Context
    {
        return new Context(null, $options);
    }
}
