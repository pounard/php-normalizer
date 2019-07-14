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

        $this->assertSame('App\Example\Tag', $context->getType('tag')->getNativeName());
    }

    public function testInvalidCircularDependencyHandlerRaiseError()
    {
        $this->expectException(\InvalidArgumentException::class);
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

        $this->assertTrue($context->shouldAlwaysGuessTypes());
        $this->assertIsArray($context->toSymfonyContext());
        $this->assertSame('application/stupid-format', $context->getFormat());
        $this->assertFalse($context->isStrict());
    }

    public function testCircularReferenceHandling()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]));

        $object1 = new \DateTimeImmutable();
        $object2 = new \DateTimeImmutable();

        $this->assertFalse($context->isCircularReference($object1));
        $this->assertTrue($context->isCircularReference($object1));

        $this->assertFalse($context->isCircularReference($object2));
        $this->assertTrue($context->isCircularReference($object2));

        $this->assertFalse($context->isCircularReference('booh'));
    }

    public function testCircularReferenceHandlingWithNoLimit()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::CIRCULAR_REFERENCE_LIMIT => 0,
        ]);

        $object1 = new \DateTimeImmutable();

        $this->assertFalse($context->isCircularReference($object1));
        $this->assertFalse($context->isCircularReference($object1));
        $this->assertFalse($context->isCircularReference($object1));
    }

    public function testHandleCircularReferenceWithNoCallbackRaiseError()
    {
        $object = new \DateTimeImmutable();
        $context = new Context(new ArrayTypeDefinitionMap([]));

        $this->expectException(CircularDependencyDetectedError::class);
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
                    $this->assertSame('some_type', $type);
                    $this->assertSame($object, $nested);
                    $this->assertInstanceOf(Context::class, $context);
                }
        ]);

        $this->assertFalse($called);
        $context->handleCircularReference('some_type', $object);
        $this->assertTrue($called);
    }

    public function testHandleCircularReferenceWithSymfonyCompatibility()
    {
        $called = false;
        $object = new \DateTimeImmutable();

        $context = new Context(new ArrayTypeDefinitionMap([]), [
            NormalizeOption::CIRCULAR_REFERENCE_HANDLER =>
                function ($nested, $format, $context) use (&$called, $object) {
                    $called = true;
                    $this->assertSame('json', $format);
                    $this->assertSame($object, $nested);
                    $this->assertIsArray($context);
                }
        ], true);

        $this->assertFalse($called);
        $context->handleCircularReference('some_type', $object);
        $this->assertTrue($called);
    }

    public function testEnterLeave()
    {
        $context = new Context(new ArrayTypeDefinitionMap([]));

        $this->assertSame(0, $context->getDepth());

        $context->enter();
        $context->enter();
        $this->assertSame(2, $context->getDepth());

        $context->leave();
        $this->assertSame(1, $context->getDepth());
    }
}
