<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithFloat
{
    /** @var float */
    private $float;

    public function getValue()
    {
        return $this->float;
    }
}
