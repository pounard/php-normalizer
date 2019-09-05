<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class DefaultTypeDefinitionTest extends TestCase
{
    private function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/array_definition.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    public function testTypeOverride()
    {
        
    }
}
