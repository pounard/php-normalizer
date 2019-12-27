<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

/**
 * Generated class naming strategy.
 */
interface NamingStrategy
{
    /**
     * Generate normalizer class name.
     *
     * @param string $userClassName
     *   Your domain class to be (de)normalized FQDN.
     * @param string $generatedClassNamespace
     *   PSR-4 prefix of generated normalizer class.
     *
     * @return string
     *   Fully qualified normalizer class name.
     */
    public function generateClassName(string $userClassName, string $generatedClassNamespace): string;

    /**
     * Generate full filename in which the normalizer class will be generated.
     *
     * @param string $realClassName
     *   Normalizer class FQDN.
     * @param string $generatedClassesTargetDir
     *   Path where to store generated normalizer class, usually the root of
     *   your PSR-4 code directory root (eg. "PROJECT_DIR/src/").
     * @param ?string $namespacePrefix
     *   Your PSR-4 namespace prefix (eg. "MyVendor\MyApp\") which must be in
     *   sync with the PSR-4 code directory root.
     *
     * @return string
     */
    public function generateFilename(string $realClassName, string $generatedClassesTargetDir, ?string $namespacePrefix = null): string;
}
