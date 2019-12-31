<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Normalizer;

use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Normalizer;
use MakinaCorpus\Normalizer\RuntimeError;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithFloat;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithInt;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableInt;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithNullableObject;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithObject;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockClassWithString;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\MockHelper;
use MakinaCorpus\Normalizer\Tests\Unit\Mock\Composition\MockClassWithScalars;
use PHPUnit\Framework\TestCase;

abstract class AbstractNormalizerTest extends TestCase
{
    /**
     * Create normalizer for testing.
     */
    abstract protected function createNormalizer(): Normalizer;

    /**
     * Create very empty context.
     */
    private function createContext(): Context
    {
        return new Context();
    }

    /**
     * Data provider.
     */
    public final function dataNormalizer(): array
    {
        return [
            [$this->createNormalizer(), $this->createContext()],
        ];
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testIntAsStringNormalizeGivesInt(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithInt(), [
            'int' => '12',
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertSame(12, $values['int']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testIntAsStringDenormalizeAllowed(Normalizer $normalizer, Context $context): void
    {
        $object = $normalizer->denormalize(MockClassWithInt::class, ['int' => 37], $context);

        self::assertInstanceOf(MockClassWithInt::class, $object);
        self::assertSame(37, $object->getValue());
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testIntAsFloatNormalizeIsCast(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithInt(), [
            'int' => '11.87',
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertSame(11, $values['int']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testIntAsFloatDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithInt::class, ['int' => 12.76], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testInvalidIntAsStringDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithInt::class, ['int' => 'this is not an int'], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testFloatAsStringNormalizeGivesFloat(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithFloat(), [
            'float' => '11.87',
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertSame(11.87, $values['float']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testFloatAsIntStringNormalizeGivesFloat(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithFloat(), [
            'float' => '11',
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertSame(11.0, $values['float']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testFloatAsStringDenormalizeAllowed(Normalizer $normalizer, Context $context): void
    {
        $object = $normalizer->denormalize(MockClassWithFloat::class, ['float' => '12.6798'], $context);

        self::assertInstanceOf(MockClassWithFloat::class, $object);
        self::assertSame(12.6798, $object->getValue());
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testInvalidFloatAsStringNormalizeIsCast(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithFloat(), [
            'float' => 'wrong!',
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertSame(0.0, $values['float']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testInvalidFloatAsStringDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithFloat::class, ['float' => 'wrong!'], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testToStringObjectNormalizeGivesString(Normalizer $normalizer, Context $context): void
    {
        $value = new class ("2019-12-30 19:23:00") extends \DateTime {
            public function __toString(): string {
                return $this->format('H:i d/m/Y');
            }
        };

        $object = $normalizer->denormalize(MockClassWithString::class, ['string' => $value], $context);

        self::assertInstanceOf(MockClassWithString::class, $object);
        self::assertSame('19:23 30/12/2019', $object->getValue());
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testToStringObjectDenormalizeGivesString(Normalizer $normalizer, Context $context): void
    {
        $value = new class ("2019-12-30 19:28:00") extends \DateTime {
            public function __toString(): string {
                return $this->format('H:i d/m/Y');
            }
        };

        $object = MockHelper::changeObjectProperties(new MockClassWithString(), [
            'string' => $value,
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertSame('19:28 30/12/2019', $values['string']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNonToStringObjectDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithString::class, ['string' => new \DateTime()], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullableScalarNormalizeAllowed(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithNullableInt(), [
            'nullableInt' => null,
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertNull($values['nullableInt']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullableScalarDenormalizeAllowed(Normalizer $normalizer, Context $context): void
    {
        $object = $normalizer->denormalize(MockClassWithNullableInt::class, ['nullableInt' => null], $context);

        self::assertInstanceOf(MockClassWithNullableInt::class, $object);
        self::assertNull($object->getValue());
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNonNullableScalarNormalizePass(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithString(), [
            'string' => null,
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertNull($values['string']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNonNullableScalarDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithString::class, ['string' => null], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullableObjectNormalizeAllowed(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithNullableObject(), [
            'nullableObject' => null,
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertNull($values['nullableObject']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullableObjectDenormalizeAllowed(Normalizer $normalizer, Context $context): void
    {
        $object = $normalizer->denormalize(MockClassWithNullableObject::class, ['nullableObject' => null], $context);

        self::assertInstanceOf(MockClassWithNullableObject::class, $object);
        self::assertNull($object->getValue());
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNonNullableObjectNormalizePass(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(new MockClassWithObject(), [
            'object' => null,
        ]);

        $values = $normalizer->normalize($object, $context);

        self::assertNull($values['object']);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullableObjectDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithObject::class, ['object' => null], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testMissingNonNullableScalarDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithString::class, [], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testMissingNonNullableObjectDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithObject::class, [], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullNonNullableScalarDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithFloat::class, ['float' => null], $context);
    }

    /**
     * @dataProvider dataNormalizer()
     */
    public function testNullNonNullableObjectDenormalizeRaiseError(Normalizer $normalizer, Context $context): void
    {
        self::expectException(RuntimeError::class);

        $normalizer->denormalize(MockClassWithObject::class, ['object' => null], $context);
    }

    /**
     * OK, I'm sorry for this one, it tests pretty much all scalar typess.
     *
     * @todo refactor this smart with a data provider for each type.
     *
     * @dataProvider dataNormalizer()
     */
    public function testScalarNormalize(Normalizer $normalizer, Context $context): void
    {
        $object = MockHelper::changeObjectProperties(
            new MockClassWithScalars,
            [
                'nullableInt' => 11,
                'int' => 29,
                'intArray' => [1, 3, 5],
                'nullableFloat' => 12.3,
                'float' => 29.45,
                'floatArray' => [4.5, 1.0, 12, 14.5],
                'nullableBool' => false,
                'bool' => true,
                'boolArray' => [false, true, true, false, true],
                'nullableString' => 'this a nullable string',
                'string' => 'this is a non nullable string',
                'stringArray' => ['this', 'is a', 'string'],
            ]
        );

        
    }
}
