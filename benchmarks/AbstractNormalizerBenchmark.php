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

/**
 * Default base class for hydration benchmarks
 */
abstract class AbstractNormalizerBenchmark
{
    /** @var \MakinaCorpus\Normalizer\Context */
    protected $context;

    /** @var \MakinaCorpus\Normalizer\DefaultNormalizer */
    protected $defaultNormalizer;

    /** @var \Symfony\Component\Serializer\Serializer */
    protected $symfonyNormalizer;

    /** @var \MakinaCorpus\Normalizer\TypeDefinitionMap */
    protected $typeDefinitionMap;

    /** @var mixed[] */
    protected $data;

    private function generateNameArray(\Faker\Generator $faker, ?int $size = null)
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
    protected function createArticleData(int $count = 5)
    {
        $this->data = [];

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < $count; ++$i) {
            $this->data[] = [
                'id' => (string)Uuid::uuid4(),
                'createdAt' => (string)($faker->dateTimeThisCentury)->format(\DateTime::ISO8601),
                'updatedAt' => (string)($faker->dateTimeThisCentury)->format(\DateTime::ISO8601),
                'authors' => $this->generateNameArray($faker),
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
    }

    /**
     * Create arbitrary data
     */
    protected function createMessageData(int $count = 5)
    {
        $this->data = [];

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < $count; ++$i) {
            $this->data[] = [
                'orderId' => $faker->randomDigitNotNull,
                'productId' => $faker->randomDigitNotNull,
                'amount' => $faker->randomDigit,
            ];
        }
    }

    /**
     * Create type definitions
     */
    protected function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/definitions.yaml');

        return $this->typeDefinitionMap = new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    /**
     * Create default normalizer
     */
    protected function createDefaultNormalizer(): DefaultNormalizer
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
    protected function createContext(array $options = []): Context
    {
        return $this->context = new Context($this->typeDefinitionMap, $options);
    }

    /**
     * Create Symfony serializer
     */
    protected function createSymfonyNormalizer(): Serializer
    {
        $classMetadataFactory = new ClassMetadataFactory(
            new LoaderChain([
                new AnnotationLoader(new AnnotationReader()),
            ])
        );
        $serializerExtracor = new SerializerExtractor($classMetadataFactory);
        $reflectionExtractor = new ReflectionExtractor();
        $propertyTypeExtractor = new PropertyInfoExtractor(
            [
                $serializerExtracor
            ],
            [
                new PhpDocExtractor(),
                $reflectionExtractor
            ],
            [
                new PhpDocExtractor(),
                $reflectionExtractor
            ], 
            [
                $reflectionExtractor
            ], 
            [
                $reflectionExtractor
            ]
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

    /**
     * Create Symfony serializer
     */
    protected function createSymfonyCachedNormalizer(): Serializer
    {
        
    }
}