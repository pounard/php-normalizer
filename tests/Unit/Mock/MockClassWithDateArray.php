<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithDateArray
{
    /** @var \DateTime[] */
    private $dateArray;

    public function getValue()
    {
        return $this->dateArray;
    }
}
