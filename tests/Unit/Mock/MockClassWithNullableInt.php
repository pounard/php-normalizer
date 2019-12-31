<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithNullableInt
{
    /** @var ?int */
    private $nullableInt;

    public function getValue()
    {
        return $this->nullableInt;
    }
}
