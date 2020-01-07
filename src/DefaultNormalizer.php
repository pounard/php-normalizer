<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;

/**
 * (De)Normalizer facade, glues together external normalizer chain and
 * normalization code generation.
 */
final class DefaultNormalizer implements Normalizer
{
    /** @var NormalizerRegistry */
    private $registry;

    /** @var CustomNormalizerChain */
    private $chain;

    /** @var bool */
    private $enableFallback = true;

    /**
     * Constructor
     */
    public function __construct(?NormalizerRegistry $registry, ?CustomNormalizerChain $chain = null)
    {
        $this->chain = $chain ?? new CustomNormalizerChain();
        $this->registry = $registry;
    }

    /**
     * @internal For unit testing purpose only.
     */
    public function disableFallback(): void
    {
        $this->enableFallback = false;
    }

    /**
     * Allow external implementations.
     *
     * Hardcode some scalar types, defer on external implementations.
     */
    private function externalNormalisation($object, Context $context): ValueOption
    {
        $type = Helper::getType($object);
        $option = Helper::normalizeScalar($type, $object, $context);

        if (!$option->success) {
            if ($this->chain->supportsNormalization($type)) {
                return ValueOption::ok($this->chain->normalize($type, $object, $context));
            }
        }

        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, Context $context)
    {
        $external = $this->externalNormalisation($object, $context);
        if ($external->success) {
            return $external->value;
        }

        $nativeType = Helper::getType($object);
        $normalizer = $this->registry->find($nativeType);

        if (!$normalizer || !\class_exists($normalizer)) {
            if ($this->enableFallback) {
                return FallbackNormalizer::normalize($object, $context);
            }
            throw new RuntimeError(\sprintf("Could not find class normalizer for type '%s'", $nativeType));
        }

        return \call_user_func(
            [$normalizer, 'normalize'],
            $object,
            $context,
            \Closure::fromCallable([$this, 'normalize'])
        );
    }

    /**
     * Allow external implementations.
     *
     * Hardcode some scalar types, defer on external implementations.
     */
    private function externalDenormalisation(string $type, $input, Context $context): ValueOption
    {
        $option = Helper::denormalizeScalar($type, $input, $context);

        if (!$option->success) {
            if ($this->chain->supportsDenormalization($type)) {
                return ValueOption::ok($this->chain->denormalize($type, $input, $context));
            }
        }

        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(string $type, $input, Context $context)
    {
        $nativeType = $context->getNativeType($type);

        $external = $this->externalDenormalisation($nativeType, $input, $context);
        if ($external->success) {
            return $external->value;
        }

        $normalizer = $this->registry->find($nativeType);

        if (!$normalizer || !\class_exists($normalizer)) {
            if ($this->enableFallback) {
                return FallbackNormalizer::denormalize($type, $input, $context);
            }
            throw new RuntimeError(\sprintf("Could not find class denormalizer for type '%s'", $nativeType));
        }

        return \call_user_func(
            [$normalizer, 'denormalize'],
            $input,
            $context,
            \Closure::fromCallable([$this, 'denormalize'])
        );
    }
}
