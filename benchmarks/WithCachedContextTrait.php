<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\MemoryTypeDefinitionMapCache;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use Symfony\Component\Yaml\Yaml;

trait WithCachedContextTrait
{
    /** @var \MakinaCorpus\Normalizer\Context */
    private $context;

    /**
     * Create cached type definitions
     */
    private function createCachedTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/definitions.yaml');

        return new MemoryTypeDefinitionMapCache([
            // Expose only aliases, to be more fair to Symfony's serializer.
            new ArrayTypeDefinitionMap([], $data['type_aliases']),
            new ReflectionTypeDefinitionMap()
        ]);
    }

    /**
     * Create context.
     */
    private function createContext(array $options = []): Context
    {
        return new Context($this->createCachedTypeDefinitionMap(), $options);
    }
}
