<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use Doctrine\Common\Annotations\AnnotationReader;
use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\DateNormalizer;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\ScalarNormalizer;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\UuidNormalizer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\LoaderChain;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Yaml\Yaml;
use MakinaCorpus\Normalizer\MemoryTypeDefinitionMapCache;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;

final class ObjectGenerator
{
    /**
     * Generate a name array
     */
    private static function generateNameArray(\Faker\Generator $faker, ?int $size = null): array
    {
        $ret = [];
        $size = $size ?? \rand(0,5);
        for ($i = 0; $i < $size; $i++) {
            $ret[] = $faker->name;
        }
        return $ret;
    }

    /**
     * Create arbitrary data
     */
    public static function createArticles(int $count = 5, bool $withId = true)
    {
        $ret = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $count; ++$i) {
            $ret[] = [
                'id' => $withId ? (string)Uuid::uuid4() : null,
                'createdAt' => (string)($faker->dateTimeThisCentury)->format(\DateTime::ISO8601),
                'updatedAt' => (string)($faker->dateTimeThisCentury)->format(\DateTime::ISO8601),
                'authors' => self::generateNameArray($faker),
                'title' => $faker->sentence,
                'text' => [
                    'value' => $faker->text,
                    'format' => 'application/text+html',
                ],
                'foo' => $faker->jobTitle,
                'bar' => $faker->randomDigitNotNull,
                'baz' => $faker->company,
                'filename' => $faker->freeEmail,
            ];
        }
        return $ret;
    }

    /**
     * Create arbitrary data
     */
    public static function createMessages(int $count = 5)
    {
        $ret = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $count; ++$i) {
            $ret[] = [
                'orderId' => $faker->randomDigitNotNull,
                'productId' => $faker->randomDigitNotNull,
                'amount' => $faker->randomDigit,
            ];
        }
        return $ret;
    }
}

/**
 * This benchmark initialization methods.
 */
trait NormalizerBenchmarkTrait
{
    /** @var \MakinaCorpus\Normalizer\Context */
    private $cachedContext;

    /** @var \MakinaCorpus\Normalizer\Context */
    private $context;

    /** @var \MakinaCorpus\Normalizer\DefaultNormalizer */
    private $defaultNormalizer;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $symfonyNormalizer;

    /**
     * Use this method for benchmark setup
     */
    private function initializeComponents(): void
    {
        $this->createTypeDefinitionMap();
        $this->createDefaultNormalizer();
        $this->createCachedContext();
        $this->createContext();
        $this->createSymfonyNormalizer();
    }

    /**
     * Create cached type definitions
     */
    private function createCachedTypeDefinitionMap(): TypeDefinitionMap
    {
        return new MemoryTypeDefinitionMapCache([
            new ReflectionTypeDefinitionMap()
        ]);
    }

    /**
     * Create type definitions
     */
    private function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/definitions.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    /**
     * Create default normalizer
     */
    private function createDefaultNormalizer(): DefaultNormalizer
    {
        return $this->defaultNormalizer = new DefaultNormalizer([
            new ScalarNormalizer(),
            new DateNormalizer(),
            new UuidNormalizer(),
        ]);
    }

    /**
     * Create context
     */
    private function createCachedContext(array $options = []): Context
    {
        return $this->cachedContext = new Context($this->createCachedTypeDefinitionMap(), $options);
    }

    /**
     * Create context
     */
    private function createContext(array $options = []): Context
    {
        return $this->context = new Context($this->createTypeDefinitionMap(), $options);
    }

    /**
     * Create Symfony serializer
     */
    private function createSymfonyNormalizer(): Serializer
    {
        // We do not test using a the CacheClassMetadataFactory implementation
        // because its impact is invisible in the bench result. Symfony
        // normalizer spend most of its time in setAttributeValue(), because it
        // uses the property-access component, which is terribly slow. Nobody
        // should ever use this on a production environment.
        $classMetadataFactory = new ClassMetadataFactory(
            new LoaderChain([
                new AnnotationLoader(new AnnotationReader()),
            ])
        );

        $serializerExtracor = new SerializerExtractor($classMetadataFactory);
        $reflectionExtractor = new ReflectionExtractor();
        $propertyTypeExtractor = new PropertyInfoExtractor(
            [$serializerExtracor],
            [new PhpDocExtractor(), $reflectionExtractor],
            [new PhpDocExtractor(), $reflectionExtractor], 
            [$reflectionExtractor], 
            [$reflectionExtractor]
        );

        return $this->symfonyNormalizer = new Serializer([
            new DateTimeNormalizer(),
            new ObjectNormalizer(
                $classMetadataFactory,
                /* NameConverterInterface $nameConverter = */ null,
                /* $propertyTypeExtractor */ null,
                $propertyTypeExtractor,
                /* ClassDiscriminatorResolverInterface $classDiscriminatorResolver = */ null
            ),
            new \MakinaCorpus\Normalizer\Bridge\Symfony\UuidNormalizer(),
        ]);
    }
}
