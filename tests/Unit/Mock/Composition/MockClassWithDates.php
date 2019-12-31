<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock\Composition;

class MockClassWithDates
{
    /** @var ?\DateTime */
    private $nullableDateTime;

    /** @var \DateTime */
    private $dateTime;

    /** @var \DateTime[] */
    private $dateTimeArray = [];

    /** @var ?\DateTimeInterface */
    private $nullableDateTimeInterface;

    /** @var \DateTimeInterface */
    private $dateTimeInterface;

    /** @var \DateTimeInterface[] */
    private $dateTimeInterfaceArray = [];

    /** @var ?\DateTimeImmutable */
    private $nullableDateTimeImmutable;

    /** @var \DateTimeImmutable */
    private $dateTimeImmutable;

    /** @var \DateTimeImmutable[] */
    private $dateTimeImmutableArray = [];
}
