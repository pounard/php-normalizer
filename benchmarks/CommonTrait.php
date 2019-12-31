<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use Doctrine\Common\Annotations\AnnotationReader;
use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\ContextFactory;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\MemoryTypeDefinitionMapCache;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\NormalizerProxy;
use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\UuidNormalizer as SymfonyUuidNormalizer;
use MakinaCorpus\Normalizer\Generator\Generator;
use MakinaCorpus\Normalizer\Generator\GeneratorRuntime;
use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use MakinaCorpus\Normalizer\Generator\Iterations\Normalizer5;
use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;
use MakinaCorpus\Normalizer\Normalizer\DateTimeNormalizer as CustomDateTimeNormalizer;
use MakinaCorpus\Normalizer\Normalizer\ScalarNormalizer;
use MakinaCorpus\Normalizer\Normalizer\UuidNormalizer as CustomUuidNormalizer;
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

if (!\function_exists('display_or_not')) {
    function display_or_not($values)
    {
        // print_r($values);
    }
}

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

        $hydrator1 = \Closure::bind(static function (MockArticle $object) use ($faker) {
            $object->id = Uuid::uuid4();
            $object->createdAt = $faker->dateTimeThisCentury;
            $object->updatedAt = $faker->dateTimeThisCentury;
            $object->authors = ObjectGenerator::generateNameArray($faker);
            $object->foo = $faker->jobTitle;
            $object->bar = $faker->randomDigitNotNull;
            $object->baz = $faker->randomFloat();
            $object->filename = $faker->freeEmail;
        }, null, MockArticle::class);

        $hydrator2 = \Closure::bind(static function (MockWithTitle $object) use ($faker) {
            $object->title = $faker->sentence;
        }, null, MockWithTitle::class);

        $hydrator3 = \Closure::bind(static function (MockWithText $object) use ($faker) {
            $object->text = new MockTextWithFormat($faker->text, 'application/text+html');
        }, null, MockWithText::class);

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = $object = new MockArticle();
            call_user_func($hydrator1, $object);
            call_user_func($hydrator2, $object);
            call_user_func($hydrator3, $object);
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

    /** @var \MakinaCorpus\Normalizer\FallbackNormalizer */
    private $fallbackNormalizer;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $symfonyNormalizer;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $symfonyNormalizerProxy;

    /** @var Normalizer5 */
    private $normalizer5;

    /** @var Normalizer */
    private $normalizer6;

    /** @var Normalizer */
    private $normalizer7;

    /** @var Normalizer */
    private $normalizer8;

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
        $this->fallbackNormalizer = $this->createFallbackNormalizer();
        $this->normalizer5 = $this->createNormalizer5();
        $this->normalizer6 = $this->createNormalizer6();
        $this->normalizer7 = $this->createNormalizer7();
        $this->normalizer8 = $this->createNormalizer8();
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
     * Create fallback normalizer
     */
    private function createFallbackNormalizer(): Normalizer
    {
        return new DefaultNormalizer(
            new class implements Generator
            {
                public function getNormalizerClass(string $className): ?string
                {
                    return null;
                }

                public function generateNormalizerClass(string $className): string
                {
                    throw new \Exception("Not implemeted.");
                }
            },
            new CustomNormalizerChain([
                new ScalarNormalizer(),
                new CustomDateTimeNormalizer(),
                new CustomUuidNormalizer()
            ])
        );
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
    private function createNormalizer5(): Normalizer5
    {
        return new Normalizer5(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated5')
            )
        );
    }

    /**
     * Create iteration 6 normalizer
     */
    private function createNormalizer6(): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated5')
            ),
            new CustomNormalizerChain([
                new CustomDateTimeNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

    /**
     * Create iteration 7 normalizer
     */
    private function createNormalizer7(): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated7')
            ),
            new CustomNormalizerChain([
                new CustomDateTimeNormalizer(),
                new CustomUuidNormalizer()
            ]),
        );
    }

    /**
     * Create iteration 8 normalizer
     */
    private function createNormalizer8(): Normalizer
    {
        return new DefaultNormalizer(
            new GeneratorRuntime(
                new Psr4AppNamingStrategy('Normalizer', 'Generated8')
            ),
            new CustomNormalizerChain([
                new CustomDateTimeNormalizer(),
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
                $this->fallbackNormalizer
            ),
        ]);
    }
}
