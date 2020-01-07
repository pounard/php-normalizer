<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;
use MakinaCorpus\Normalizer\Mock\MockTextWithFormat;
use MakinaCorpus\Normalizer\Mock\MockWithText;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

final class ReflectionTypeDefinitionMapTest extends TestCase
{
    private function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $propertyTypeExtractor = new PropertyInfoExtractor(
            [],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        );

        $ret = new ReflectionTypeDefinitionMap();
        $ret->setTypeInfoExtractor($propertyTypeExtractor);

        return $ret;
    }

    public function testNonExistingTypeRaiseError()
    {
        $map = $this->createTypeDefinitionMap();

        self::expectException(TypeDoesNotExistError::class);
        self::expectExceptionMessageRegExp("/Class does.*'non_existing'/");
        $map->get('non_existing');
    }

    public function testExistsWithExistingClass()
    {
        $map = $this->createTypeDefinitionMap();

        self::assertTrue($map->exists(MockTextWithFormat::class));
    }

    public function testExistsWithNonExistingClass()
    {
        $map = $this->createTypeDefinitionMap();

        self::assertFalse($map->exists('non_existing'));
    }

    public function testSimpleUseCase()
    {
        self::doesNotPerformAssertions();

        $map = $this->createTypeDefinitionMap();
        $map->get(MockWithText::class);
    }
}
