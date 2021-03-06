<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Mapping\Factory\CacheClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\LoaderChain;

/**
 * In benchmarks using this, I'm not sure I gave the Symfony serializer enough
 * components and configuration to ensure maximum meta-information cachability
 * which means I might be cheating the benchmarks.
 *
 * At least we do test (de)normalization using a set of objects, which
 * drastically mitigates this, since most of its metadata registries will
 * statically cache everything it can.
 *
 * On the opposite side, we don't provide it either of the annotation reader
 * and most of its default normalizers (there's a lot) which means it's
 * actually faster than it would be in a Symfony application.
 *
 * Also, you have to note that we tuned our test class so that it won't crash
 * because yes, there's a few corner cases it is unable to handle, sadly.
 */
trait WithSymfonyClassMetadataTrait
{
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
        $classMetadataFactory = new CacheClassMetadataFactory(
            $classMetadataFactory = new ClassMetadataFactory(
                new LoaderChain([
                    new AnnotationLoader(new AnnotationReader()),
                ])
            ),
            new ApcuAdapter('SymfonyMetadata')
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
}
