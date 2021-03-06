<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithNullableObject
{
    /** @var ?MockClassWithNullableInt */
    private $nullableObject;

    public function getValue()
    {
        return $this->nullableObject;
    }
}
