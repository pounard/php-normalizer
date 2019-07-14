<?php

require_once __DIR__.'/vendor/autoload.php';

use Doctrine\Common\Annotations\AnnotationReader;
use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\DateNormalizer;
use MakinaCorpus\Normalizer\DefaultNormalizer;
use MakinaCorpus\Normalizer\MemoryTypeDefinitionMapCache;
use MakinaCorpus\Normalizer\ReflectionTypeDefinitionMap;
use MakinaCorpus\Normalizer\ScalarNormalizer;
use MakinaCorpus\Normalizer\UuidNormalizer;
use MakinaCorpus\Normalizer\Benchmarks\MockArticle;
use MakinaCorpus\Normalizer\Benchmarks\ObjectGenerator;
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

/* ****************************************************************************
 *
 * Symfony normalizer
 *
 ***************************************************************************** */

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

$symfonyNormalizer = new Serializer([
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

/* ****************************************************************************
 *
 * Custom normalizer (non dynamic)
 *
 ***************************************************************************** */

$defaultNormalizer = new DefaultNormalizer([
    new ScalarNormalizer(),
    new DateNormalizer(),
    new UuidNormalizer(),
]);
$config = Yaml::parseFile(__DIR__.'/benchmarks/definitions.yaml');

$context = new Context(
    new ArrayTypeDefinitionMap($config['types'], $config['type_aliases'])
);

/* ****************************************************************************
 *
 * Custom normalizer (dynamic)
 *
 ***************************************************************************** */

$reflectionTypeDefinition = new ReflectionTypeDefinitionMap();
$reflectionTypeDefinition->setTypeInfoExtractor($propertyTypeExtractor);

$dynamicContext = new Context(
    new MemoryTypeDefinitionMapCache([
        // new ArrayTypeDefinitionMap($config['types'], $config['type_aliases']),
        $reflectionTypeDefinition,
    ])
);

/* ****************************************************************************
 *
 * Data and test
 *
 ***************************************************************************** */

function separator(string $text, $rawData = null): void {
    echo "\n\n--\n-- ", $text, "\n--\n\n";
    if ($rawData) {
        // print_r($rawData);
    }
}

function timer_start(): float {
    return microtime(true);
}

function timer_stop(float $start, string $message = "Duration"): string {
    return $message . ": " . round((microtime(true) - $start) * 1000) . "ms";
}

$originalData = ObjectGenerator::createArticles(200, false);

separator("Original data", $originalData);

// Custom
$objects = [];
$timer = timer_start();
foreach ($originalData as $data) {
    $objects[] = $defaultNormalizer->denormalize(MockArticle::class, $data, $dynamicContext);
}
separator(timer_stop($timer, "Custom denormalized"), $objects);
$data = [];
$timer = timer_start();
foreach ($objects as $object) {
    $data[] = $defaultNormalizer->normalize(MockArticle::class, $object, $dynamicContext);
}
separator(timer_stop($timer, "Custom normalized"), $data);

/*
// Symfony
$objects = [];
$timer = timer_start();
foreach ($originalData as $data) {
    $objects[] = $symfonyNormalizer->denormalize($data, MockArticle::class);
}
separator(timer_stop($timer, "Symfony denormalized"), $objects);
/*
$data = [];
$timer = timer_start();
foreach ($objects as $object) {
    $data[] = $symfonyNormalizer->normalize($object);
}
separator(timer_stop($timer, "Symfony normalized"), $data);
 */


//print_r();
//print_r($defaultNormalizer->denormalize('app.article', $data, $context));
//print_r($defaultNormalizer->denormalize(MockArticle::class, $data, $dynamicContext));
