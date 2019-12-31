<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithInt
{
    /** @var int */
    private $int;

    public function getValue()
    {
        return $this->int;
    }
}
