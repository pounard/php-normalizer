<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock\Composition;

class MockClassWithNumbers
{
    /** @var ?int */
    private $nullableInt;

    /** @var int */
    private $int;

    /** @var int[] */
    private $intArray = [];

    /** @var ?float */
    private $nullableFloat;

    /** @var float */
    private $float;

    /** @var float[] */
    private $floatArray = [];
}
