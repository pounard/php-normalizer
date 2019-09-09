<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\DateNormalizer;
use MakinaCorpus\Normalizer\InvalidValueTypeError;
use MakinaCorpus\Normalizer\Option;
use MakinaCorpus\Normalizer\UnsupportedTypeError;
use PHPUnit\Framework\TestCase;

final class NormalizerDateTest extends TestCase
{
    private function createContext(?string $format = null)
    {
        return new Context(new ArrayTypeDefinitionMap([]), [
            Option::DATE_FORMAT => $format,
        ]);
    }

    /**
     * Data provider
     */
    public function getSupportedClassList()
    {
        return [
            [\DateTime::class, \DateTime::class],
            [\DateTimeInterface::class, \DateTimeImmutable::class],
            [\DateTimeImmutable::class, \DateTimeImmutable::class],
        ];
    }

    /**
     * @dataProvider getSupportedClassList
     */
    public function testNormalizeDate(string $type, string $class)
    {
        $context = $this->createContext();
        $normalizer = new DateNormalizer();

        $object = new \DateTime("2019-06-22");
        $this->assertRegExp('/^2019-06-22T/', $normalizer->normalize($type, $object, $context));
    }

    /**
     * @dataProvider getSupportedClassList
     */
    public function testNormalizeDateWithFormat(string $type, string $class)
    {
        $context = $this->createContext('d/m/Y');
        $normalizer = new DateNormalizer();

        $object = new \DateTime("2019-06-22");
        $this->assertSame('22/06/2019', $normalizer->normalize($type, $object, $context));
    }

    public function testNormalizeRaiseExceptionWithInvalidType()
    {
        $context = $this->createContext();
        $normalizer = new DateNormalizer();

        $this->expectException(UnsupportedTypeError::class);
        $normalizer->normalize('BWAAAA', new \DateTime(), $context);
    }

    public function testNormalizeRaiseExceptionWithInvalidValue()
    {
        $context = $this->createContext();
        $normalizer = new DateNormalizer();

        $this->expectException(InvalidValueTypeError::class);
        $normalizer->normalize(\DateTimeInterface::class, 'BWAAAA', $context);
    }

    /**
     * @dataProvider getSupportedClassList
     */
    public function testDenormalizeDate(string $type, string $class)
    {
        $context = $this->createContext();
        $normalizer = new DateNormalizer();

        $object = $normalizer->denormalize($type, '2019-06-22', $context);
        $this->assertInstanceOf($class, $object);
        $this->assertSame('2019 06 22', $object->format('Y m d'));
    }

    /**
     * @dataProvider getSupportedClassList
     */
    public function testDenormalizeDateWithFormat(string $type, string $class)
    {
        $context = $this->createContext('d/m/Y');
        $normalizer = new DateNormalizer();

        $object = $normalizer->denormalize($type, '22/06/2019', $context);
        $this->assertInstanceOf($class, $object);
        $this->assertSame('2019 06 22', $object->format('Y m d'));
    }

    public function testDenormalizeRaiseExceptionWithInvalidType()
    {
        $context = $this->createContext();
        $normalizer = new DateNormalizer();

        $this->expectException(UnsupportedTypeError::class);
        $normalizer->denormalize('BWAAAA', '2019-06-22', $context);
    }

    /**
     * @dataProvider getSupportedClassList
     */
    public function testDenormalizeRaiseExceptionWithInvalidValue(string $type)
    {
        $context = $this->createContext();
        $normalizer = new DateNormalizer();

        $this->expectException(InvalidValueTypeError::class);
        $normalizer->denormalize($type, 'BWAAAA', $context);
    }
}
