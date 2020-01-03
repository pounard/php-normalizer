<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\CircularDependencyDetectedError;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\NormalizeOption;
use MakinaCorpus\Normalizer\Option;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class ContextTest extends TestCase
{
    private function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/array_definition.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    public function testContextGetType()
    {
        $context = new Context($this->createTypeDefinitionMap());

        self::assertSame('App\Example\Tag', $context->getType('tag')->getNativeName());
    }

    public function testInvalidCircularDependencyHandlerRaiseError()
    {
        self::expectException(\InvalidArgumentException::class);
        new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::CIRCULAR_REFERENCE_HANDLER => 'not a callable'
        ]);
    }

    public function testSomeEvidentGetters()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::ALWAYS_GUESS_TYPE => true,
            Option::SERIALIATION_FORMAT => 'application/stupid-format',
        ]);

        self::assertIsArray($context->toSymfonyContext());
        self::assertSame('application/stupid-format', $context->getFormat());
    }

    public function testCircularReferenceHandling()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]));

        $object1 = new \DateTimeImmutable();
        $object2 = new \DateTimeImmutable();

        self::assertFalse($context->isCircularReference($object1));
        self::assertTrue($context->isCircularReference($object1));

        self::assertFalse($context->isCircularReference($object2));
        self::assertTrue($context->isCircularReference($object2));

        self::assertFalse($context->isCircularReference('booh'));
    }

    public function testCircularReferenceHandlingWithNoLimit()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::CIRCULAR_REFERENCE_LIMIT => 0,
        ]);

        $object1 = new \DateTimeImmutable();

        self::assertFalse($context->isCircularReference($object1));
        self::assertFalse($context->isCircularReference($object1));
        self::assertFalse($context->isCircularReference($object1));
    }

    public function testHandleCircularReferenceWithNoCallbackRaiseError()
    {
        $object = new \DateTimeImmutable();
        $context = new Context(new ArrayTypeDefinitionMap([]));

        self::expectException(CircularDependencyDetectedError::class);
        $context->handleCircularReference('some_type', $object);
    }

    public function testHandleCircularReference()
    {
        $called = false;
        $object = new \DateTimeImmutable();

        $context = new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::CIRCULAR_REFERENCE_HANDLER =>
                function (string $type, $nested, $context) use (&$called, $object) {
                    $called = true;
                    self::assertSame('some_type', $type);
                    self::assertSame($object, $nested);
                    self::assertInstanceOf(Context::class, $context);
                }
        ]);

        self::assertFalse($called);
        $context->handleCircularReference('some_type', $object);
        self::assertTrue($called);
    }

    public function testHandleCircularReferenceWithSymfonyCompatibility()
    {
        $called = false;
        $object = new \DateTimeImmutable();

        $context = new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::CIRCULAR_REFERENCE_HANDLER =>
                function ($nested, $format, $context) use (&$called, $object) {
                    $called = true;
                    self::assertSame('json', $format);
                    self::assertSame($object, $nested);
                    self::assertIsArray($context);
                }
        ], true);

        self::assertFalse($called);
        $context->handleCircularReference('some_type', $object);
        self::assertTrue($called);
    }

    public function testEnterLeave()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]));

        self::assertSame(0, $context->getDepth());

        $context->enter();
        $context->enter();
        self::assertSame(2, $context->getDepth());

        $context->leave();
        self::assertSame(1, $context->getDepth());
    }
}
