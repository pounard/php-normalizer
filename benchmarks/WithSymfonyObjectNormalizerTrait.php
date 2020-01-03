<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use MakinaCorpus\Normalizer\Bridge\Symfony\Serializer\Normalizer\UuidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

trait WithSymfonyObjectNormalizerTrait
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
            new ObjectNormalizer(
                $classMetadataFactory,
                /* NameConverterInterface $nameConverter = */ null,
                /* PropertyAccessorInterface $propertyAccessor */ null,
                $propertyTypeExtractor,
                /* ClassDiscriminatorResolverInterface $classDiscriminatorResolver = */ null
            ),
        ]);
    }
}
