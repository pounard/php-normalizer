<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Generated class naming strategy
 */
interface NamingStrategy
{
    /**
     * Generate class name
     *
     * @param string $userClassName
     * @param string $generatedClassNamespace
     *
     * @return string
     *   Fully qualified namespace
     */
    public function generateClassName(string $userClassName, string $generatedClassNamespace): string;

    /**
     * Generate full filename in which the file will be saved
     *
     * @param string $realClassName
     * @param string $generatedClassesTargetDir
     * @param ?string $namespacePrefix
     *   When working in a PSR-4 namespace, this is the namespace prefix
     *
     * @return string
     */
    public function generateFilename(string $realClassName, string $generatedClassesTargetDir, ?string $namespacePrefix = null): string;
}

/**
 * Image your app is within the following namespace:
 *
 *   MyVendor\MyApp\...
 *
 * And your entity class into:
 *
 *   MyVendor\MyApp\Domain\Model\SomeEntity
 *
 * This will attempt to create such target class:
 *
 *   MyVendor\MyApp\Normalizer\Domain\Model\SomeEntityNormalizer
 *
 * And place it within the associated folder.
 *
 * "\Normalizer\" infix and "Normalizer" class name suffix can be configured
 * If explicitely set null or an empty string, then the previous scenario will
 * generate the following normalizer class name:
 *
 *   MyVendor\MyApp\Domain\Model\SomeEntity
 *
 * Which conflicts with entity name. File name will always respect PSR-4.
 * If your code base is PSR-0, just write as PSR-4 prefix the full path
 * until the namespace infix, it will work transparently.
 *
 * It seems very specific, I agree, but it fits well in Symfony-like organised
 * applications, and will keep the (de)normalizers outside of your domain.
 */
final class Psr4AppNamingStrategy implements NamingStrategy
{
    private $classNameSuffix = null;
    private $namespaceInfix = null;

    public function __construct(?string $classNameSuffix = 'Normalizer', ?string $namespaceInfix = 'Normalizer')
    {
        $this->classNameSuffix = $classNameSuffix;
        $this->namespaceInfix = $namespaceInfix;
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
