<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithObjectArray
{
    /** @var MockClassWithNullableInt[] */
    private $objectArray;

    public function getValue()
    {
        return $this->objectArray;
    }
}
