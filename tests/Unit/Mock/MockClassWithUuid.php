<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithUuid
{
    /** @var ?\Ramsey\Uuid\UuidInterface */
    private $uuid;

    public function getValue()
    {
        return $this->uuid;
    }
}
