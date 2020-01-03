<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\UuidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

trait WithSymfonyGetSetNormalizerTrait
{
    use WithSymfonyClassMetadataTrait;

    /** @var Serializer */
    private $serializer;

    /**
     * Create Symfony serializer
     */
    private function createSymfonyNormalizer(): Serializer
    {
        list($classMetadataFactory, $propertyTypeExtractor) = $this->prepareSymfonyInternals();

        return new Serializer([
            new DateTimeNormalizer(),
            new UuidNormalizer(),
            new GetSetMethodNormalizer(
                $classMetadataFactory,
                /* NameConverterInterface $nameConverter = */ null,
                $propertyTypeExtractor,
                /* ClassDiscriminatorResolverInterface $classDiscriminatorResolver = */ null
            ),
        ]);
    }
}
