<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\PropertyDefinition;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\TypeDoesNotExistError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class ArrayTypeDefinitionTest extends TestCase
{
    private function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/array_definition.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    public function testNonExistingTypeRaiseError()
    {
        $map = $this->createTypeDefinitionMap();

        self::expectException(TypeDoesNotExistError::class);
        self::expectExceptionMessageRegExp("/not find.*'non_existing'/");
        $map->get('non_existing');
    }

    public function testWrongAliasRaiseError()
    {
        $map = $this->createTypeDefinitionMap();

        self::expectException(TypeDoesNotExistError::class);
        self::expectExceptionMessageRegExp("/'wrong_alias' maps.*non existing.*'non_existing_type/");
        $map->get('wrong_alias');
    }

    public function testTypeAliasLookup()
    {
        $map = $this->createTypeDefinitionMap();

        $type = $map->get('tag');
        self::assertSame('App\Example\Tag', $type->getNativeName());
    }

    public function testAliasOverridesTypes()
    {
        $type = $this->createTypeDefinitionMap()->get('ConflictingType');

        self::assertSame('App\Example\Tag', $type->getNativeName());
    }

    public function testAliasAlwaysMapToNativeType()
    {
        $type = $this->createTypeDefinitionMap()->get('conflicting_type');

        self::assertSame('ConflictingType', $type->getNativeName());
    }

    public function testIsTerminal()
    {
        $map = $this->createTypeDefinitionMap();

        $type = $map->get('article');
        self::assertFalse($type->isTerminal());

        $type = $map->get('date');
        self::assertTrue($type->isTerminal());
    }

    public function testNormalizedName()
    {
        $type = $this->createTypeDefinitionMap()->get('article');

        self::assertSame('App\Example\Article', $type->getNativeName());
        self::assertSame('example.app.article', $type->getNormalizedName());
    }

    public function testNormalizeNameFallbackOnNativeName()
    {
        $type = $this->createTypeDefinitionMap()->get('tag');

        self::assertSame('App\Example\Tag', $type->getNativeName());
        self::assertSame('App\Example\Tag', $type->getNormalizedName());
    }

    public function testPropertyGet()
    {
        $type = $this->createTypeDefinitionMap()->get('article');
        $properties = $type->getProperties();

        self::assertSame(['id', 'title', 'author', 'tags', 'content', 'date'], \array_keys($properties));

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        foreach ($properties as $key => $prop) {
            self::assertInstanceOf(PropertyDefinition::class, $prop);
            self::assertSame($key, $prop->getNativeName());
            self::assertSame('App\Example\Article', $prop->getOwnerType());
        }
    }

    public function testPropertyDefaults()
    {
        $type = $this->createTypeDefinitionMap()->get('article');
        $properties = $type->getProperties();

        self::assertSame(['id', 'title', 'author', 'tags', 'content', 'date'], \array_keys($properties));

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        $prop = $properties['id'];

        // Defaults
        self::assertSame('id', $prop->getNormalizedName(), "Normalized name defaults to native name");
        self::assertSame([], $prop->getAliases(), "Aliases defaults to empty");
        self::assertSame([], $prop->getGroups(), "Groups defaults to empty");
        self::assertFalse($prop->isOptional(), "Optional defaults to false");
        self::assertFalse($prop->isCollection(), "Collection defaults to false");
        self::assertNull($prop->getCollectionType(), "Collection type defaults to null");

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        $prop = $properties['author'];
        self::assertTrue($prop->isCollection());
        self::assertSame('array', $prop->getCollectionType(), "Collection type defaults to array when collection");
    }

    public function testPropertyValueSet()
    {
        $type = $this->createTypeDefinitionMap()->get('article');
        $properties = $type->getProperties();

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        $prop = $properties['tags'];

        // Required values are here
        self::assertSame('example.app.tag', $prop->getTypeName());

        // All values are set in YAML
        self::assertSame('tag_collection', $prop->getNormalizedName());
        self::assertSame(['labels'], $prop->getAliases());
        self::assertSame(['editorial', 'triage'], $prop->getGroups());
        self::assertTrue($prop->isOptional());
        self::assertTrue($prop->isCollection());
        self::assertSame('array', $prop->getCollectionType());
    }
}
