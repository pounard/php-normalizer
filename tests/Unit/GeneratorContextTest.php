<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\GeneratorContext;
use MakinaCorpus\Normalizer\RuntimeError;
use PHPUnit\Framework\TestCase;

final class GeneratorContextTest extends TestCase
{
    public function testAddDependency()
    {
        $context = new GeneratorContext();

        $context->addClassDependency('App\\Domain\\Model\\SomeClass');
        $context->addClassDependency('\\App\\Domain\\Model\\SomeClass');
        $context->addClassDependency('Other\\SomeClass');

        self::assertSame(
            [
                'App\\Domain\\Model\\SomeClass',
                'Other\\SomeClass',
            ],
            $context->getClassDependencies()
        );
    }

    public function testAddImportReturnShortName()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass');
        self::assertSame('SomeClass', $alias);

        self::assertSame(
            [
                'SomeClass' => 'App\\Domain\\Model\\SomeClass',
            ],
            $context->getImports()
        );
    }

    public function testAddImportWillTrimBackslash()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('\\App\\Domain\\Model\\SomeClass');
        self::assertSame('SomeClass', $alias);

        self::assertSame(
            [
                'SomeClass' => 'App\\Domain\\Model\\SomeClass',
            ],
            $context->getImports()
        );
    }

    public function testAddImportWillNotDuplicateClass()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass');
        self::assertSame('SomeClass', $alias);

        $alias = $context->addImport('\\App\\Domain\\Model\\SomeClass');
        self::assertSame('SomeClass', $alias);

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass');
        self::assertSame('SomeClass', $alias);

        self::assertSame(
            [
                'SomeClass' => 'App\\Domain\\Model\\SomeClass',
            ],
            $context->getImports()
        );
    }

    public function testAddImportResolveAliasConflict()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass');
        self::assertSame('SomeClass', $alias);

        $alias = $context->addImport('\\Another\\Vendor\\SomeClass');
        self::assertSame('SomeClass1', $alias);

        $alias = $context->addImport('YetAnother\\SomeClass');
        self::assertSame('SomeClass12', $alias);

        self::assertSame(
            [
                'SomeClass' => 'App\\Domain\\Model\\SomeClass',
                'SomeClass1' => 'Another\\Vendor\\SomeClass',
                'SomeClass12' => 'YetAnother\\SomeClass',
            ],
            $context->getImports()
        );
    }

    public function testAddImportCanAlias()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass', 'MyEntity');
        self::assertSame('MyEntity', $alias);

        self::assertSame(
            [
                'MyEntity' => 'App\\Domain\\Model\\SomeClass',
            ],
            $context->getImports()
        );
    }

    public function testAddImportWithAlreadyAliasedClassReturnRightAlias()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass', 'MyEntity');
        self::assertSame('MyEntity', $alias);

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass');
        self::assertSame('MyEntity', $alias);

        self::assertSame(
            [
                'MyEntity' => 'App\\Domain\\Model\\SomeClass',
            ],
            $context->getImports()
        );
    }

    public function testAddImportCannotRealias()
    {
        $context = new GeneratorContext();

        $alias = $context->addImport('App\\Domain\\Model\\SomeClass', 'MyEntity');
        self::assertSame('MyEntity', $alias);

        self::expectException(RuntimeError::class);
        $context->addImport('App\\Domain\\Model\\SomeClass', 'OtherAlias');
    }
}
