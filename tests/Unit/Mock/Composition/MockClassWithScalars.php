<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock\Composition;

class MockClassWithScalars extends MockClassWithNumbers
{
    /** @var ?bool */
    private $nullableBool;

    /** @var bool */
    private $bool;

    /** @var bool[] */
    private $boolArray = [];

    /** @var ?string */
    private $nullableString;

    /** @var string */
    private $string;

    /** @var string[] */
    private $stringArray = [];
}
