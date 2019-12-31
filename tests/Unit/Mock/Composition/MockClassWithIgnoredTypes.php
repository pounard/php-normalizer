<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock\Composition;

class MockClassWithIgnoredTypes
{
    /** @var ?resource */
    private $nullableResource;

    /** @var resource */
    private $resource;

    /** @var resource[] */
    private $resourceArray = [];

    /** @var ?callable */
    private $nullableCallable;

    /** @var callable */
    private $callable;

    /** @var callable[] */
    private $callableArray = [];
}
