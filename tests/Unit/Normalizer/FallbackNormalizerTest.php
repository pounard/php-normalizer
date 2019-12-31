<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Normalizer;

use MakinaCorpus\Normalizer\FallbackNormalizer;
use MakinaCorpus\Normalizer\Normalizer;

final class FallbackNormalizerTest extends AbstractNormalizerTest
{
    protected function createNormalizer(): Normalizer
    {
        return FallbackNormalizer::create();
    }
}
