<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithIntArray
{
    /** @var int[] */
    private $intArray;

    public function getValue()
    {
        return $this->intArray;
    }
}
