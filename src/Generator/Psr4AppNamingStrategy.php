<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

/**
 * Image your app is within the following namespace:
 *
 *   \MyVendor\MyApp\...
 *
 * And your entity class into:
 *
 *   \MyVendor\MyApp\Model\SomeEntity
 *
 * This will attempt to create such target class:
 *
 *   \MyVendor\MyApp\[NAMESPACE_INFIX]\Model\SomeEntity[CLASS_NAME_SUFFIX]
 *
 * Per default, namespace infix and class name suffix are "Normalizer":
 *
 *   \MyVendor\MyApp\Normalizer\Model\SomeEntityNormalizer
 *
 * And place it within the configured generated classes target folder.
 *
 * Namespace infix and class name suffix can be configured via the constructor
 * arguments.
 *
 * If explicitely set to null or an empty string, then the previous scenario
 * will generate the following normalizer class name:
 *
 *   \MyVendor\MyApp\Domain\Model\SomeEntity
 *
 * Which conflicts with entity name. File name will always respect PSR-4.
 * If your code base is PSR-0, just write as PSR-4 prefix the full path
 * until the namespace infix, it will work transparently.
 *
 * Default configuration reflect your domain naming strategy, while mirroring
 * it into its own folder. It allows you to easily add the generated classes
 * into your distributable packages. This way, you never will have to generate
 * anything on a production environment.
 */
final class Psr4AppNamingStrategy implements NamingStrategy
{
    /** @var ?string */
    private $classNameSuffix;

    /** @var ?string */
    private $namespaceInfix;

    /** @var ?string */
    private $defaultNamespacePrefix;

    /**
     * Default constructor
     */
    public function __construct(?string $classNameSuffix = 'Normalizer', ?string $namespaceInfix = 'Normalizer', ?string $defaultNamespacePrefix = null)
    {
        $this->classNameSuffix = $classNameSuffix;
        $this->namespaceInfix = $namespaceInfix;
        $this->defaultNamespacePrefix = $defaultNamespacePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function generateClassName(string $userClassName, string $generatedClassNamespace): string
    {
        $userClassName = \rtrim($userClassName);
        // Include trailing '\' in namespace name in order to avoid false
        // positive such as "Test" matching "Testing" for example.
        $generatedClassNamespace = \trim($generatedClassNamespace, '\\').'\\';

        $prefixLength = \strlen($generatedClassNamespace);
        if (\substr($userClassName, 0, $prefixLength) === $generatedClassNamespace) {
            $userClassName = \substr($userClassName, $prefixLength);
        }
        if ($this->namespaceInfix) {
            $generatedClassNamespace .= $this->namespaceInfix."\\";
        }

        return $generatedClassNamespace.$userClassName.$this->classNameSuffix;
    }

    /**
     * {@inheritdoc}
     */
    public function generateFilename(string $realClassName, string $generatedClassesTargetDir, ?string $namespacePrefix = null): string
    {
        $realClassName = \trim($realClassName, '\\');

        $namespacePrefix = $namespacePrefix ?? $this->defaultNamespacePrefix;

        if ($namespacePrefix) {
            $namespacePrefix = \trim($namespacePrefix, '\\').'\\';
            $prefixLength = \strlen($namespacePrefix);
            if (\substr($realClassName, 0, $prefixLength) === $namespacePrefix) {
                $realClassName = \substr($realClassName, $prefixLength);
            }
        }

        return \rtrim($generatedClassesTargetDir, '//')
            .'/'
            .($this->namespaceInfix ? $this->namespaceInfix . '/' : '')
            .\str_replace("\\", "/", $realClassName)
            .$this->classNameSuffix
            .'.php'
        ;
    }
}
