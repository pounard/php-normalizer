<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

class MockClassWithObjects extends MockClassWithDates
{
    /** @var ?MockClassWithObjects */
    private $nullableSelf;

    /** @var MockClassWithObjects */
    private $self;

    /** @var MockClassWithObjects[] */
    private $selfArray = [];

    /** @var ?MockClassWithScalars */
    private $nullableClassWithScalars;

    /** @var MockClassWithScalars */
    private $classWithScalars;

    /** @var MockClassWithScalars[] */
    private $classWithScalarsArray = [];
}
