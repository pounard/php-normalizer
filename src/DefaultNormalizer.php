<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use GeneratedHydrator\Configuration;
use Goat\Hydrator\HydratorInterface;
use Goat\Hydrator\HydratorMap;

/**
 * Object normalizer and denormalizer
 */
final class DefaultNormalizer implements Normalizer, Denormalizer
{
    private $denormalizers = [];
    private $denormalizersCache = [];
    private $hydratorMap = [];
    private $normalizers = [];
    private $normalizersCache = [];

    /**
     * @param Normalizer[]|Denormalizer[] $implementations
     */
    public function __construct(iterable $implementations = [])
    {
        foreach ($implementations as $instance) {
            $this->register($instance);
        }
        if (!$this->hydratorMap) {
            $this->hydratorMap = new HydratorMap(new Configuration('foo'));
        }
    }

    /**
     * Register a single (de)normalizer
     *
     * @param Normalizer|Denormalizer $instance
     */
    public function register($instance)
    {
        if ($instance instanceof Denormalizer) {
            $this->denormalizers[] = $instance;
        }
        if ($instance instanceof Normalizer) {
            $this->normalizers[] = $instance;
        }
    }

    /**
     * Find denormalizer for type
     */
    private function findDenormalizer(string $type): ?Normalizer
    {
        if (isset($this->denormalizersCache[$type])) {
            return $this->denormalizersCache[$type];
        }
        /** @var \MakinaCorpus\Normalizer\Denormalizer $instance */
        foreach ($this->denormalizers as $instance) {
            if ($instance->supportsDenormalization($type)) {
                return $this->denormalizersCache[$type] = $instance;
            }
        }
        return null;
    }

    /**
     * Find normalizer for type
     */
    private function findNormalizer(string $type): ?Normalizer
    {
        if (isset($this->normalizersCache[$type])) {
            return $this->normalizersCache[$type];
        }
        /** @var \MakinaCorpus\Normalizer\Normalizer $instance */
        foreach ($this->normalizers as $instance) {
            if ($instance->supportsNormalization($type)) {
                return $this->normalizersCache[$type] = $instance;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(string $type): bool
    {
        return null !== $this->findNormalizer($type);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(string $type): bool
    {
        return null !== $this->findDenormalizer($type);
    }

    /**
     * Extract object raw values
     */
    private function extract(string $type, $object)
    {
        return $this->hydratorMap->getRealHydrator($type)->extractValues($object);
    }

    /**
     * Handle collection normalization
     */
    private function normalizeCollection(string $type, $collection, Context $context)
    {
        $context->enter();

        try {
            // @todo Going throught this means the object in memory is wrong
            //    but it's still preferable to be resilient than strict
            if (!\is_iterable($collection)) {
                return [$this->normalize($collection, $context, $type)];
            }

            $ret = [];

            foreach ($collection as $key => $object) {
                $ret[$key] = $this->normalize($type, $object, $context);
            }

            return $ret;

        } finally {
            $context->leave();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        $context->enter();

        try {
            $type = $context->getNativeType($type);

            if ($normalizer = $this->findNormalizer($type)) {
                return $normalizer->normalize($type, $object, $context);
            }

            $typeDef = $context->getType($type);

            if ($context->isCircularReference($object)) {
                return $context->handleCircularReference($type, $object);
            }

            $ret = [];
            $data = $this->extract($typeDef->getNativeName(), $object);
            $properties = $typeDef->getProperties();

            /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
            foreach ($properties as $property) {
                $name = $property->getNativeName();

                if (isset($data[$name])) {
                    if ($property->isCollection()) {
                        $ret[$property->getNormalizedName()] = $this->normalizeCollection($property->getTypeName(), $data[$name], $context);
                    } else {
                        $ret[$property->getNormalizedName()] = $this->normalize($property->getTypeName(), $data[$name], $context);
                    }
                }
            }

            return $ret;

        } finally {
            $context->leave();
        }
    }

    /**
     * Hydrate object instance
     */
    private function hydrate(string $type, array $data)
    {
        return $this->hydratorMap->getRealHydrator($type)->createAndHydrateInstance($data, HydratorInterface::CONSTRUCTOR_SKIP);
    }

    /**
     * Handle collection denormalization
     */
    private function denormalizeCollection(string $type, $data, Context $context)
    {
        $context->enter();

        try {
            // Automatically fix values not being array while they should be.
            // @todo should we consider iterator implementations or traversables
            //   being treated as arrays?
            if (!\is_array($data)) {
                return [$this->denormalize($type, $data, $context)];
            }

            $ret = [];

            foreach ($data as $key => $value) {
               $ret[$key] = $this->denormalize($type, $value, $context);
            }

            return $ret;

        } finally {
            $context->leave();
        }
    }

    /**
     * Find and extract the property value within the given data array
     */
    private function extractPropertyFromNormalizedData(PropertyDefinition $property, $data)
    {
        if (\is_array($data)) {
            foreach ($property->getCandidateNames() as $name) {
                if (\array_key_exists($name, $data)) {
                    return $data[$name];
                }
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        $context->enter();

        try {
            $type = $context->getNativeType($type);

            if ($normalizer = $this->findDenormalizer($type)) {
                return $normalizer->denormalize($type, $data, $context);
            }

            $values = [];
            $typeDef = $context->getType($type);

            // Pre-computed properties handle blacklisting.
            /** @var \MakinaCorpus\Normalizer\PropertyDefinition $property */
            foreach ($typeDef->getProperties() as $property) {
                $name = $property->getNativeName();

                // Extract function handles property name aliasing.
                $value = $this->extractPropertyFromNormalizedData($property, $data);

                if (null === $value) {
                    // Error on null properties is actually configurable in more
                    // than one way, here:
                    //   - object can be partially hydrated by configuration at
                    //     type level,
                    //   - hydration context can be set to non strict mode, case
                    //     in which all errors are allowed.
                    if (!$property->isOptional() && $context->isStrict()) {
                        throw new \InvalidArgumentException(\sprintf(
                            "Property '%s' of type '%s' cannot be null (candidate aliases: %s)",
                            $name, $type, \implode(', ', $property->getCandidateNames())
                        ));
                    }
                    $values[$name] = null;
                } else if ($property->isCollection()) {
                    $values[$name] = $this->denormalizeCollection($property->getTypeName(), $value, $context);
                } else {
                    $values[$name] = $this->denormalize($property->getTypeName(), $value, $context);
                }
            }

            return $this->hydrate($typeDef->getNativeName(), $values);

        } finally {
            $context->leave();
        }
    }
}
