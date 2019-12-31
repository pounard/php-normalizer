<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithObject
{
    /** @var MockClassWithNullableInt */
    private $object;

    public function getValue()
    {
        return $this->object;
    }
}
