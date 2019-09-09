<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use Doctrine\Common\Annotations\AnnotationReader;
use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\DateNormalizer;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\MemoryTypeDefinitionMapCache;
use MakinaCorpus\Normalizer\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\ScalarNormalizer;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\UuidNormalizer as CustomUuidNormalizer;
use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\NormalizerProxy;
use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\UuidNormalizer as SymfonyUuidNormalizer;
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

final class ObjectGenerator
{
    /**
     * Generate a name array
     */
    public static function generateNameArray(\Faker\Generator $faker, ?int $size = null): array
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
                    'text' => $faker->text,
                    'format' => 'application/text+html',
                ],
                'foo' => $faker->jobTitle,
                'bar' => $faker->randomDigitNotNull,
                'baz' => $faker->randomFloat(),
                'filename' => $faker->freeEmail,
            ];
        }

        return $ret;
    }

    /**
     * Create arbitrary data
     */
    public static function createAndHydrateArticles(int $count = 5, bool $withId = true)
    {
        $ret = [];

        $faker = \Faker\Factory::create();

        $hydrator = \Closure::bind(static function () use ($faker) {
            $object = new MockArticle();
            $object->id = Uuid::uuid4();
            $object->createdAt = $faker->dateTimeThisCentury;
            $object->updatedAt = $faker->dateTimeThisCentury;
            $object->authors = ObjectGenerator::generateNameArray($faker);
            $object->title = $faker->sentence;
            $object->text = new MockTextWithFormat($faker->text, 'application/text+html');
            $object->foo = $faker->jobTitle;
            $object->bar = $faker->randomDigitNotNull;
            $object->baz = $faker->company;
            $object->filename = $faker->freeEmail;
            return $object;
        }, null, MockArticle::class);

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = call_user_func($hydrator);
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
                'orderId' => (string)Uuid::uuid4(),
                'productId' => $faker->randomDigitNotNull,
                'amount' => $faker->randomFloat(),
            ];
        }

        return $ret;
    }

    /**
     * Create arbitrary data
     */
    public static function createAndHydrateMessages(int $count = 5, bool $withId = true)
    {
        $ret = [];

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = new AddToCartMessage(Uuid::uuid4(), $faker->randomDigitNotNull, $faker->randomDigit);
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

    /** @var \Symfony\Component\Serializer\Serializer */
    private $symfonyNormalizerProxy;

    /** @var \Normalizer5 */
    private $normalizer5;

    /** @var \Normalizer6 */
    private $normalizer6;

    /** @var \Normalizer6 */
    private $normalizer7;

    private function getContextWithReflection(): Context
    {
        return $this->cachedContext = $this->cachedContext->fresh();
    }

    private function getContextWithConfigOnly(): Context
    {
        return $this->context = $this->context->fresh();
    }

    /**
     * Use this method for benchmark setup
     */
    private function initializeComponents(): void
    {
        $this->cachedContext = $this->createCachedContext();
        $this->context = $this->createContext();
        $this->defaultNormalizer = $this->createDefaultNormalizer();
        $this->normalizer5 = $this->createNormalizer5();
        $this->normalizer6 = $this->createNormalizer6();
        $this->normalizer7 = $this->createNormalizer7();
        $this->symfonyNormalizer = $this->createSymfonyNormalizer();
        $this->symfonyNormalizerProxy = $this->createSymfonyProxy();
    }

    /**
     * Create cached type definitions
     */
    private function createCachedTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/definitions.yaml');

        return new MemoryTypeDefinitionMapCache([
            // Expose only aliases, to be more fair to Symfony's serializer
            new ArrayTypeDefinitionMap([], $data['type_aliases']),
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
        return new DefaultNormalizer([
            new ScalarNormalizer(),
            new DateNormalizer(),
            new CustomUuidNormalizer()
        ]);
    }

    /**
     * Create context
     */
    private function createCachedContext(array $options = []): Context
    {
        return new Context($this->createCachedTypeDefinitionMap(), $options);
    }

    /**
     * Create context
     */
    private function createContext(array $options = []): Context
    {
        return new Context($this->createTypeDefinitionMap(), $options);
    }

    /**
     * Create iteration 5 normalizer
     */
    private function createNormalizer5(): \Normalizer5
    {
        return new \Normalizer5(
            new \Generator5Runtime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated5')
            )
        );
    }

    /**
     * Create iteration 6 normalizer
     */
    private function createNormalizer6(): \Normalizer6
    {
        return new \Normalizer6(
            new \Generator5Runtime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated5')
            ),
            new \NormalizerChain6([
                new DateNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

        /**
     * Create iteration 6 normalizer
     */
    private function createNormalizer7(): \Normalizer6
    {
        return new \Normalizer6(
            new \Generator5Runtime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated7')
            ),
            new \NormalizerChain6([
                new DateNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

    /**
     * Prepare some Symfony stuff
     */
    private function prepareSymfonyInternals(): array
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

        return [$classMetadataFactory, $propertyTypeExtractor];
    }

    /**
     * Create Symfony serializer
     */
    private function createSymfonyNormalizer(): Serializer
    {
        list($classMetadataFactory, $propertyTypeExtractor) = $this->prepareSymfonyInternals();

        return new Serializer([
            new DateTimeNormalizer(),
            new SymfonyUuidNormalizer(),
            new ObjectNormalizer(
                $classMetadataFactory,
                /* NameConverterInterface $nameConverter = */ null,
                /* PropertyAccessorInterface $propertyAccessor */ null,
                $propertyTypeExtractor,
                /* ClassDiscriminatorResolverInterface $classDiscriminatorResolver = */ null
            ),
        ]);
    }

    private function createSymfonyProxy()
    {
        return new Serializer([
            new NormalizerProxy(
                new ContextFactory(
                    $this->createCachedTypeDefinitionMap()
                ),
                $this->defaultNormalizer
            ),
        ]);
    }
}
