<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\Generator\Psr4AppNamingStrategy;
use PHPUnit\Framework\TestCase;

final class Psr4AppNamingStrategyTest extends TestCase
{
    public function testNormalUseCase()
    {
        $strategy = new Psr4AppNamingStrategy();

        $projectSourceDirectory = '/var/www/my-app/src';
        $projectPsr4Namespace = 'MyVendor\\MyApp';
        $entityClass = 'MyVendor\\MyApp\\Domain\\Model\\SomeEntity';

        $classname = $strategy->generateClassName($entityClass, $projectPsr4Namespace);
        self::assertSame('MyVendor\\MyApp\\Normalizer\\Domain\\Model\\SomeEntityNormalizer', $classname);

        $filename = $strategy->generateFilename($entityClass, $projectSourceDirectory, $projectPsr4Namespace);
        self::assertSame('/var/www/my-app/src/Normalizer/Domain/Model/SomeEntityNormalizer.php', $filename);
    }

    public function testWithDiffentSuffixAndInfix()
    {
        $strategy = new Psr4AppNamingStrategy('Foo', 'Bar');

        $projectSourceDirectory = '/var/www/my-app/src';
        $projectPsr4Namespace = 'MyVendor\\MyApp';
        $entityClass = 'MyVendor\\MyApp\\Domain\\Model\\SomeEntity';

        $classname = $strategy->generateClassName($entityClass, $projectPsr4Namespace);
        self::assertSame('MyVendor\\MyApp\\Bar\\Domain\\Model\\SomeEntityFoo', $classname);

        $filename = $strategy->generateFilename($entityClass, $projectSourceDirectory, $projectPsr4Namespace);
        self::assertSame('/var/www/my-app/src/Bar/Domain/Model/SomeEntityFoo.php', $filename);
    }

    public function testWithNoSuffixAndInfix()
    {
        $strategy = new Psr4AppNamingStrategy(null, null);

        $projectSourceDirectory = '/var/www/my-app/src';
        $projectPsr4Namespace = 'MyVendor\\MyApp';
        $entityClass = 'MyVendor\\MyApp\\Domain\\Model\\SomeEntity';

        $classname = $strategy->generateClassName($entityClass, $projectPsr4Namespace);
        self::assertSame('MyVendor\\MyApp\\Domain\\Model\\SomeEntity', $classname);

        $filename = $strategy->generateFilename($entityClass, $projectSourceDirectory, $projectPsr4Namespace);
        self::assertSame('/var/www/my-app/src/Domain/Model/SomeEntity.php', $filename);
    }

    /*
    public function testGeneratedClassesOutsideNamespace()
    {
        $strategy = new Psr4AppNamingStrategy();

        $projectSourceDirectory = '/var/www/my-app/src';
        $projectPsr4Namespace = 'MyVendor\\MyApp';
        $entityClass = 'MyVendor\\MyApp\\Domain\\Model\\SomeEntity';

        $classname = $strategy->generateClassName($entityClass, $projectPsr4Namespace);
        self::assertSame('MyVendor\\MyApp\\Normalizer\\Domain\\Model\\SomeEntityNormalizer', $classname);

        $filename = $strategy->generateFilename($entityClass, $projectSourceDirectory, $projectPsr4Namespace);
        self::assertSame('/var/www/my-app/src/Normalizer/Domain/Model/SomeEntityNormalizer.php', $filename);
    }
     */
}
