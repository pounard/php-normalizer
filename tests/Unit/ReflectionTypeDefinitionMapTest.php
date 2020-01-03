<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use PHPUnit\Framework\TestCase;

final class ReflectionTypeDefinitionMapTest extends TestCase
{
    /**
     * Data provider
     */
    public static function dataExtractTypesFromDocBlockSimple()
    {
        // Non nullables non collections
        yield ["/** @var string */", 'string', false, false];
        yield ["/** @var \DateTime */", 'DateTime', false, false];

        // Nullable use cases
        yield ["/** @var ?bool */", 'bool', true, false];
        yield ["/** @var string|null */", 'string', true, false];
        yield ["/** @var null|int */", 'int', true, false];

        // Various collections
        yield ["/** @var \DateTime[] */", 'DateTime', false, true];
        yield ["/** @var ?\DateTimeInterface[] */", 'DateTimeInterface', true, true];
        yield ["/** @var null|string[] */", 'string', true, true];

        // If an unsupported type is present, all are nullable.
        yield ["/** @var null|string|callable */", 'string', true, false];
        yield ["/** @var ?string|callable */", 'string', true, false];
        yield ["/** @var string|callable */", 'string', true, false];
    }

    /**
     * @dataProvider dataExtractTypesFromDocBlockSimple
     */
    public function testExtractTypesFromDocBlockSimple($docBlock, string $expected, bool $optional, bool $collection)
    {
        $types = ReflectionTypeDefinitionMap::extractTypesFromDocBlock($docBlock);

        self::assertCount(1, $types);
        self::assertSame($expected, $types[0]['type']);
        self::assertSame($collection, $types[0]['collection']);
        self::assertSame($optional, $types[0]['optional']);
    }

    public function testResolveTypeFromClassPropertyWithFqdn()
    {
        self::markTestIncomplete();
    }

    public function testResolveTypeFromClassPropertyWithLocalName()
    {
        self::markTestIncomplete();
    }

    public function testResolveTypeFromClassPropertyFromUseStatements()
    {
        self::markTestIncomplete();
    }
}
