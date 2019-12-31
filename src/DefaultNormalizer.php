<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

use MakinaCorpus\Normalizer\Generator\Generator;
use MakinaCorpus\Normalizer\Normalizer\CustomNormalizerChain;

/**
 * (De)Normalizer facade, glues together external normalizer chain and
 * normalization code generation.
 */
final class DefaultNormalizer implements Normalizer
{
    /** @var Generator */
    private $generator;

    /** @var CustomNormalizerChain */
    private $chain;

    /** @var bool */
    private $enableFallback = true;

    /**
     * Constructor
     */
    public function __construct(?Generator $generator, ?CustomNormalizerChain $chain = null)
    {
        $this->chain = $chain ?? new CustomNormalizerChain();
        $this->generator = $generator;
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

        switch ($type) {
            case 'bool':
                return ValueOption::ok($object);
            case 'float':
                return ValueOption::ok($object);
            case 'int':
                return ValueOption::ok($object);
            case 'null':
                return ValueOption::ok($object);
            case 'string':
                return ValueOption::ok($object);
        }

        if ($this->chain->supportsNormalization($type)) {
            return ValueOption::ok($this->chain->normalize($type, $object, $context));
        }

        return ValueOption::miss();
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
        $normalizer = $this->generator->getNormalizerClass($nativeType);

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
        switch ($type) {
            case 'bool':
                return ValueOption::ok($input);
            case 'float':
                return ValueOption::ok($input);
            case 'int':
                return ValueOption::ok($input);
            case 'null':
                return ValueOption::ok($input);
            case 'string':
                return ValueOption::ok($input);
        }

        if ($this->chain->supportsDenormalization($type)) {
            return ValueOption::ok($this->chain->denormalize($type, $input, $context));
        }

        return ValueOption::miss();
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

        $normalizer = $this->generator->getNormalizerClass($nativeType);

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
