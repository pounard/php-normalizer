<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Bridge\Goat;

use Goat\Hydrator\HydratorInterface;
use Goat\Hydrator\HydratorMap;
use MakinaCorpus\Normalizer\Normalizer;

final class NormalizerHydratorMap implements HydratorMap
{
    /** @var HydratorMap */
    private $decorated;

    /** @var Normalizer */
    private $normalizer;

    /** @var list<HydratorInterface> */
    private $instances;

    /**
     * Default constructor.
     */
    public function __construct(Normalizer $normalizer, ?HydratorMap $decorated = null)
    {
        $this->normalizer = $normalizer;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return true;
    }

    /**
     * Build hydrator
     */
    private function buildHydrator(string $class, string $separator = null): HydratorInterface
    {
        return new class ($class, $this->normalizer) implements HydratorInterface 
        {
            /** @var \ReflectionClass */
            private $reflectionClass;

            /** @var Normalizer */
            private $normalizer;

            public function __construct(string $class, Normalizer $normalizer)
            {
                $this->reflectionClass = new \ReflectionClass($class);
            }

            public function createAndHydrateInstance(array $values, $constructor = HydratorInterface::CONSTRUCTOR_LATE)
            {
                return $this->reflectionClass->newInstanceWithoutConstructor();
            }

            public function hydrateObject(array $values, $object)
            {
                return $this->normalizer->denormalize($object, $values);
            }

            public function extractValues($object)
            {
                return $this->normalizer->normalize($object);
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $class, string $separator = null): HydratorInterface
    {
        return $this->instances[$class] ?? (
            $this->instances[$class] = $this->buildHydrator($class, $separator)
        );
    }
}
