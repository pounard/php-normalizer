<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

use MakinaCorpus\Normalizer\WritableNormalizerRegistry;

final class StaticMapRegistry implements WritableNormalizerRegistry
{
    /** @var string */
    private $mapFilename;

    /**
     * Default constructor
     */
    public function __construct(string $mapFilename)
    {
        $this->mapFilename = $mapFilename;
    }

    /**
     * {@inheritdoc}
     */
    private function load(): array
    {
        if (!\file_exists($this->mapFilename)) {
            return [];
        }
        $map = include $this->mapFilename;
        if (!\is_array($map)) {
            \trigger_error(\sprintf("'%s': file does not contain an array", $this->mapFilename), E_USER_WARNING);
            return [];
        }
        return $map;
    }

    /**
     * Internal recursion for find() method.
     */
    private function doFind(array $map, string $className, array &$done): ?string
    {
        if ($done[$className] ?? null) {
            return null;
        }
        $done[$className] = true;

        $normalizer = $map[$className] ?? null;
        if (!$normalizer) {
            return null;
        }

        if (!$normalizerClass = $normalizer[0]) {
            \trigger_error(\sprintf("'%s': could not load class", $normalizerClass), E_USER_WARNING);
            return null;
        }

        if (!\class_exists($normalizerClass)) {
            // If class was not already registered using preloading or composer
            // attempt to force load it.
            require_once $normalizer[1];

            if (!\class_exists($normalizerClass)) {
                \trigger_error(\sprintf("'%s': class is not defined in '%s' file", $normalizerClass, $normalizer[1]), E_USER_WARNING);
                return null;
            }
        }

        if ($normalizer[2] ?? null) {
            foreach ($normalizer[2] as $dependencyClassName) {
                $this->doFind($map, $dependencyClassName, $done);
            }
        }

        return $normalizerClass;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $className): ?string
    {
        $map = $this->load();
        $done = [];

        return $this->doFind($map, $className, $done);
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $className, string $normalizerClassName, string $filename, array $dependencies = []): void
    {
        if (!\class_exists($className)) {
            throw new \LogicException(\sprintf("'%s': class does not exist", $className));
        }
        if (!\class_exists($normalizerClassName)) {
            // Attempt loading the file.
            require_once $filename;

            if (!\class_exists($normalizerClassName)) {
                throw new \LogicException(\sprintf("'%s': class does not exist", $normalizerClassName));
            }
        }

        $map = $this->load();
        $map[$className] = [$normalizerClassName, $filename, $dependencies];

        $this->writeContents($this->cleanupMap($map));
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterClass(string $className): void
    {
        $map = $this->load();

        unset($map[$className]);

        $this->writeContents($this->cleanupMap($map));
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterNormalizer(string $normalizerClassName): void
    {
        $map = $this->load();

        foreach ($map as $index => $data) {
            if ($data[0] === $normalizerClassName) {
                unset($map[$index]);
            }
        }

        $this->writeContents($this->cleanupMap($map));
    }

    /**
     * Cleanup non existing or invalid normalizers
     */
    public function cleanup(array $map): void
    {
        $this->writeContents($this->cleanupMap($this->load()));
    }

    /**
     * Cleanup non existing or invalid normalizers
     */
    private function cleanupMap(array $map): array
    {
        foreach ($map as $className => $normalizer) {
            if (!\class_exists($className)) {
                // \trigger_error(\sprintf("'%s': class does not exist", $className), E_USER_NOTICE);
                unset($map[$className]);
            }
            if (!\file_exists($normalizer[1])) {
                // \trigger_error(\sprintf("'%s': file does not exist", $normalizer[1]), E_USER_NOTICE);
            } else {
                require_once $normalizer[1];
            }
            if (!\class_exists($normalizer[0])) {
                // \trigger_error(\sprintf("'%s': class does not exist", $normalizer[0]), E_USER_NOTICE);
                unset($map[$className]);
            }
        }
        \ksort($map);

        return $map;
    }

    /**
     * Write array to file
     */
    private function writeContents(array $map): void
    {
        if (\file_exists($this->mapFilename)) {
            if (!\unlink($this->mapFilename)) {
                throw new \LogicException(\sprintf("'%s': could not delete file"));
            }
        }

        $date = (new \DateTimeImmutable())->format(\DateTime::ISO8601);
        $contents = \var_export($map, true);
        $bytes = \file_put_contents($this->mapFilename, <<<EOT
<?php
// Generated on {$date}.
return {$contents};\n
EOT
        );

        if (!$bytes) {
            throw new \LogicException(\sprintf("'%s': could not write file"));
        }
    }
}
