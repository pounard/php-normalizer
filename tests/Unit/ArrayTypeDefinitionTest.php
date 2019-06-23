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

        $this->expectException(TypeDoesNotExistError::class);
        $this->expectExceptionMessageRegExp("/'non_existing'.*not exist/");
        $map->get('non_existing');
    }

    public function testWrongAliasRaiseError()
    {
        $map = $this->createTypeDefinitionMap();

        $this->expectException(TypeDoesNotExistError::class);
        $this->expectExceptionMessageRegExp("/'wrong_alias' maps.*non existing.*'non_existing_type/");
        $map->get('wrong_alias');
    }

    public function testTypeAliasLookup()
    {
        $map = $this->createTypeDefinitionMap();

        $type = $map->get('tag');
        $this->assertSame('App\Example\Tag', $type->getNativeName());
    }

    public function testAliasOverridesTypes()
    {
        $type = $this->createTypeDefinitionMap()->get('ConflictingType');

        $this->assertSame('App\Example\Tag', $type->getNativeName());
    }

    public function testAliasAlwaysMapToNativeType()
    {
        $type = $this->createTypeDefinitionMap()->get('conflicting_type');

        $this->assertSame('ConflictingType', $type->getNativeName());
    }

    public function testIsTerminal()
    {
        $map = $this->createTypeDefinitionMap();

        $type = $map->get('article');
        $this->assertFalse($type->isTerminal());

        $type = $map->get('date');
        $this->assertTrue($type->isTerminal());
    }

    public function testNormalizedName()
    {
        $type = $this->createTypeDefinitionMap()->get('article');

        $this->assertSame('App\Example\Article', $type->getNativeName());
        $this->assertSame('example.app.article', $type->getNormalizedName());
    }

    public function testNormalizeNameFallbackOnNativeName()
    {
        $type = $this->createTypeDefinitionMap()->get('tag');

        $this->assertSame('App\Example\Tag', $type->getNativeName());
        $this->assertSame('App\Example\Tag', $type->getNormalizedName());
    }

    public function testPropertyGet()
    {
        $type = $this->createTypeDefinitionMap()->get('article');
        $properties = $type->getProperties();

        $this->assertSame(['id', 'title', 'author', 'tags', 'content', 'date'], \array_keys($properties));

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        foreach ($properties as $key => $prop) {
            $this->assertInstanceOf(PropertyDefinition::class, $prop);
            $this->assertSame($key, $prop->getNativeName());
            $this->assertSame('App\Example\Article', $prop->getOwnerType());
        }
    }

    public function testPropertyDefaults()
    {
        $type = $this->createTypeDefinitionMap()->get('article');
        $properties = $type->getProperties();

        $this->assertSame(['id', 'title', 'author', 'tags', 'content', 'date'], \array_keys($properties));

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        $prop = $properties['id'];

        // Defaults
        $this->assertSame('id', $prop->getNormalizedName(), "Normalized name defaults to native name");
        $this->assertSame([], $prop->getAliases(), "Aliases defaults to empty");
        $this->assertSame([], $prop->getGroups(), "Groups defaults to empty");
        $this->assertFalse($prop->isOptional(), "Optional defaults to false");
        $this->assertFalse($prop->isCollection(), "Collection defaults to false");
        $this->assertNull($prop->getCollectionType(), "Collection type defaults to null");

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        $prop = $properties['author'];
        $this->assertTrue($prop->isCollection());
        $this->assertSame('array', $prop->getCollectionType(), "Collection type defaults to array when collection");
    }

    public function testPropertyValueSet()
    {
        $type = $this->createTypeDefinitionMap()->get('article');
        $properties = $type->getProperties();

        /** @var \MakinaCorpus\Normalizer\PropertyDefinition $prop */
        $prop = $properties['tags'];

        // Required values are here
        $this->assertSame('example.app.tag', $prop->getTypeName());

        // All values are set in YAML
        $this->assertSame('tag_collection', $prop->getNormalizedName());
        $this->assertSame(['labels'], $prop->getAliases());
        $this->assertSame(['editorial', 'triage'], $prop->getGroups());
        $this->assertTrue($prop->isOptional());
        $this->assertTrue($prop->isCollection());
        $this->assertSame('array', $prop->getCollectionType());
    }
}
