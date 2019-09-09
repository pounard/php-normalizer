<?php
/**
 * Iteration #6.
 *
 * Make it pluggable with external custom (de)normalizers.
 */

declare(strict_types=1);

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Denormalizer;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\UnsupportedTypeError;

final class NormalizerChain6 implements Normalizer, Denormalizer
{
    private $denormalizers = [];
    private $denormalizersCache = [];
    private $normalizers = [];
    private $normalizersCache = [];

    /**
     * @param Normalizer[]|Denormalizer $implementations
     */
    public function __construct(iterable $implementations = [])
    {
        foreach ($implementations as $instance) {
            $this->register($instance);
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
    private function findDenormalizer(string $type): ?Denormalizer
    {
        if (\array_key_exists($type, $this->denormalizersCache)) {
            return $this->denormalizersCache[$type];
        }
        /** @var \MakinaCorpus\Normalizer\Denormalizer $instance */
        foreach ($this->denormalizers as $instance) {
            if ($instance->supportsDenormalization($type)) {
                return $this->denormalizersCache[$type] = $instance;
            }
        }
        return $this->denormalizersCache[$type] = null;
    }

    /**
     * Find normalizer for type
     */
    private function findNormalizer(string $type): ?Normalizer
    {
        if (\array_key_exists($type, $this->normalizersCache)) {
            return $this->normalizersCache[$type];
        }
        /** @var \MakinaCorpus\Normalizer\Normalizer $instance */
        foreach ($this->normalizers as $instance) {
            if ($instance->supportsNormalization($type)) {
                return $this->normalizersCache[$type] = $instance;
            }
        }
        return $this->normalizersCache[$type] = null;
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
     * {@inheritdoc}
     */
    public function denormalize(string $type, $data, Context $context)
    {
        if ($denormalizer = $this->findDenormalizer($type)) {
            return $denormalizer->denormalize($type, $data, $context);
        }
        throw new UnsupportedTypeError();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(string $type, $object, Context $context)
    {
        if ($normalizer = $this->findNormalizer($type)) {
            return $normalizer->normalize($type, $object, $context);
        }
        throw new UnsupportedTypeError();
    }
}

/**
 * Normalizer
 */
final class Normalizer6
{
    /** @var \Generator5 */
    private $generator;

    /** @var \NormalizerChain6 */
    private $chain;

    /**
     * Constructor
     */
    public function __construct(Generator5 $generator, NormalizerChain6 $chain)
    {
        $this->chain = $chain;
        $this->generator = $generator;
    }

    /**
     * Allow external implementations.
     *
     * Hardcode some scalar types, defer on external implementations.
     */
    private function externalDenormalisation(string $type, $input, Context $context): HydratorOption
    {
        switch ($type) {
            case 'bool':
                return HydratorOption::ok($input);
            case 'float':
                return HydratorOption::ok($input);
            case 'int':
                return HydratorOption::ok($input);
            case 'null':
                return HydratorOption::ok($input);
            case 'string':
                return HydratorOption::ok($input);
        }

        if ($this->chain->supportsDenormalization($type)) {
            return HydratorOption::ok($this->chain->denormalize($type, $input, $context));
        }

        return HydratorOption::miss();
    }

    /**
     * Dernormalise object
     */
    public function denormalize(string $type, /* string|array|T */ $input, Context $context) /* : T */
    {
        $nativeType = $context->getNativeType($type);

        $external = $this->externalDenormalisation($nativeType, $input, $context);
        if ($external->handled) {
            return $external->value;
        }

        $normalizer = $this->generator->getNormalizerClass($nativeType);

        if (!$normalizer) {
            throw new \RuntimeException("Implemeent me");
        }

        return \call_user_func(
            [$normalizer, 'denormalize'],
            $input,
            $context,
            [$this, 'denormalize']
        );
    }
}
