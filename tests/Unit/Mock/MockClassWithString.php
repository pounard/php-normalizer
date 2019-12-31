<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithString
{
    /** @var string */
    private $string;

    public function getValue()
    {
        return $this->string;
    }
}
