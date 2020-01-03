<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Normalizer;

use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\Generator\DefaultGenerator;
use MakinaCorpus\Normalizer\Generator\GeneratorRuntime;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;
use MakinaCorpus\Normalizer\Normalizer\DateTimeNormalizer;

final class GeneratorRuntimeNormalizerTest extends AbstractNormalizerTest
{
    protected function createNormalizer(): Normalizer
    {
        $generatedClassNamespace = 'MakinaCorpus\\Normalizer\\Tests\\Unit';

        $namingStrategy = new Psr4AppNamingStrategy(
            'Normalizer',
            'Generated',
            $generatedClassNamespace
        );

        $generator = new DefaultGenerator(
            new ContextFactory(
                new ReflectionTypeDefinitionMap()
            ),
            \dirname(__DIR__),
            $generatedClassNamespace,
            $namingStrategy
        );

        // Classes to generate hydrators for.
        foreach ([
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithDateArray::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithFloat::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithInt::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithIntArray::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObjectArray::class,
            \MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithString::class,
        ] as $className) {
            $generator->generateNormalizerClass($className);
        }

        $normalizer = new DefaultNormalizer(
            new GeneratorRuntime($namingStrategy, $generatedClassNamespace),
            new CustomNormalizerChain([
                new DateTimeNormalizer(),
            ])
        );
        $normalizer->disableFallback();

        return $normalizer;
    }
}