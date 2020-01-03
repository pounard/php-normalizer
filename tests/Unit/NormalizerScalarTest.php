<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\Option;
use MakinaCorpus\Normalizer\Normalizer\ScalarNormalizer;
use PHPUnit\Framework\TestCase;

final class NormalizerScalarTest extends TestCase
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
    public function getBasicTestScenario()
    {
        return [
            ['int', "12", 12],
            ['bool', 1, true],
            ['bool', 0, false],
            ['float', 12, 12.0],
            ['float', "48.2", 48.2],
            ['null', "anything", "anything"],
            ['string', "anything", "anything"],
            ['string', null, null],
        ];
    }

    /**
     * @dataProvider getBasicTestScenario
     */
    public function testNormalize(string $type, $rawValue, $targetValue)
    {
        $context = $this->createContext();
        $normalizer = new ScalarNormalizer();

        self::assertSame($targetValue, $normalizer->normalize($type, $rawValue, $context));
    }

    /**
     * @dataProvider getBasicTestScenario
     */
    public function testDenormalize(string $type, $rawValue, $targetValue)
    {
        $context = $this->createContext('d/m/Y');
        $normalizer = new ScalarNormalizer();

        self::assertSame($targetValue, $normalizer->denormalize($type, $rawValue, $context));
    }
}
