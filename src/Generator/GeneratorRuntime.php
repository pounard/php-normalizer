<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

/**
 * Generator runtime is only able to load generated normalizers, but disallows
 * runtime class generation: classes must be pre-generated using a warmup phase
 * or using packaged already generated code.
 */
final class GeneratorRuntime implements Generator
{
    /** @var NamingStrategy */
    private $namingStrategy;

    /** @var string[] */
    private $nameMap = [];

    /**
     * Constructor
     *
     * @param string $projectSourceRoot
     */
    public function __construct(?NamingStrategy $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy ?? new Psr4AppNamingStrategy();
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizerClass(string $className): ?string
    {
        $normalizerClass = $this->nameMap[$className] ?? null;
        if ($normalizerClass) {
            return $normalizerClass;
        }
        // False mean we could not generated normalizer class.
        if (false === $normalizerClass) {
            return null;
        }

        $normalizerClass = $this->namingStrategy->generateClassName($className, '\\');

        if (!\class_exists($normalizerClass)) {
            // Bool here otherwise upper ?? usage would give false negatives.
            $this->nameMap[$normalizerClass] = false;

            return null;
        }

        return $normalizerClass;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNormalizerClass(string $className): string
    {
        throw new \LogicException("You cannot wake up the generator at runtime, normalizer classes must be pre-generated.");
    }
}