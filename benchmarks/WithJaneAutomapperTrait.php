<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use Jane\AutoMapper\AutoMapper;
use Jane\AutoMapper\Mapper;
use Jane\AutoMapper\Compiler\Compiler;
use Jane\AutoMapper\Compiler\FileLoader;

/**
 * jane-php/automapper will always be faster with a 10x factor that almost all
 * other solutions, but you have to be aware of some very important facts:
 *
 * - it cannot handle classes such as those of ramsey/uuid gracefully, and
 *   will crash when attempting to (de)normalize those: because of this we
 *   disabled UUID (de)normalization in this benchmark. You have to consider
 *   the fact that it is one of the most expensive object hydration that is
 *   done during the benchmark which gives Jane an approximative 2x boost,
 *
 * - as soon as you don't have any accessors (getters or setters) on a property
 *   it can't (de)normalize it: in the actualy code, jane-php/automapper only
 *   partially handle objects, which gives an additional yet irrealistic
 *   almost 2x extra boost,
 *
 * - it doesn't provide a viable extension mecanism, everything is hardcoded,
 *   you actually cannot plug what's missing (UUID support for once),
 *
 * - it does not provide features such as property normalized names, that
 *   symfony/serializer and our own API here both have the decency to provide
 *   and do well,
 *
 * - it does not do any type validation, hence is very unreliable, it just
 *   copy values using objets setters and array access interface without caring
 *   about it, if you let an invalid value pass, PHP will throw engine errors
 *   instead of Jane handling it properly,
 * 
 * - generally it doesn't use reflection to construct objects when they don't
 *   have a constructor, pff hew.
 *
 * It is far from being production ready.
 */
trait WithJaneAutomapperTrait
{
    /** @var Mapper */
    private $mapper;

    /**
     * Create file loader
     */
    private function prepareLoader(): FileLoader
    {
        if (!\file_exists($cache = __DIR__. '/cache/jane-automapper')) {
            \mkdir($cache, 0755, true);
        }

        return new FileLoader(
            new Compiler(),
            __DIR__. '/cache/jane-automapper'
        );
    }

    /**
     * Create normalizer
     */
    private function createMapper(string $targetClass): Mapper
    {
        return AutoMapper::create(false, $this->prepareLoader())->getMapper($targetClass, 'array');
    }

    /**
     * Create denormalizer
     */
    private function createDenormalizerMapper(string $targetClass)
    {
        return AutoMapper::create(false, $this->prepareLoader())->getMapper('array', $targetClass);
    }
}
