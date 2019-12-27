<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Functional;

use MakinaCorpus\Normalizer\ArrayTypeDefinitionMap;
use MakinaCorpus\Normalizer\Context;
use MakinaCorpus\Normalizer\DateNormalizer;
use MakinaCorpus\Normalizer\FallbackNormalizer;
use MakinaCorpus\Normalizer\ScalarNormalizer;
use MakinaCorpus\Normalizer\TypeDefinitionMap;
use MakinaCorpus\Normalizer\UuidNormalizer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Yaml\Yaml;

final class NormalizationTest extends TestCase
{
    private function createTypeDefinitionMap(): TypeDefinitionMap
    {
        $data = Yaml::parseFile(__DIR__.'/definitions.yaml');

        return new ArrayTypeDefinitionMap($data['types'], $data['type_aliases']);
    }

    private function createFallbackNormalizer(): FallbackNormalizer
    {
        return new FallbackNormalizer([
            new ScalarNormalizer(),
            new DateNormalizer(),
            new UuidNormalizer(),
        ]);
    }

    public function testNormalizeSimple()
    {
        $map = $this->createTypeDefinitionMap();
        $normalizer = $this->createFallbackNormalizer();

        $data = [
            'value' => "<p>Hello, world!</p>",
            'format' => null,
        ];

        /** @var \MakinaCorpus\Normalizer\Tests\Functional\MockTextWithFormat $object */
        $object = $normalizer->denormalize(MockTextWithFormat::class, $data, new Context($map));

        $this->assertInstanceOf(MockTextWithFormat::class, $object);
        $this->assertSame("<p>Hello, world!</p>", $object->getText());
        $this->assertNull($object->getFormat());

        $values = $normalizer->normalize(MockTextWithFormat::class, $object, new Context($map));
        $this->assertSame(['value' => "<p>Hello, world!</p>"], $values);
    }

    public function testNormalizeWithComplexTypes()
    {
        $map = $this->createTypeDefinitionMap();
        $normalizer = $this->createFallbackNormalizer();

        $data = [
            'title' => "This is a test",
            'markup' => [
                'value' => "<p>Hello, world!</p>",
                'format' => 'text/html',
            ],
        ];

        /** @var \MakinaCorpus\Normalizer\Tests\Functional\MockWithText $object */
        $object = $normalizer->denormalize(MockWithText::class, $data, new Context($map));

        $this->assertInstanceOf(MockWithText::class, $object);
        $this->assertSame("This is a test", $object->getTitle());
        $this->assertSame("<p>Hello, world!</p>", $object->getMarkup()->getText());
        $this->assertSame("text/html", $object->getMarkup()->getFormat());

        $values = $normalizer->normalize(MockWithText::class, $object, new Context($map));
        $this->assertSame($data, $values);
    }

    public function testNormalizeWithArrayCollection()
    {
        $map = $this->createTypeDefinitionMap();
        $normalizer = $this->createFallbackNormalizer();

        $id = Uuid::uuid4();
        $createdAt = new \DateTimeImmutable();

        $data = [
            'id' => (string)$id,
            'createdAt' => $createdAt->format(\DateTime::ISO8601),
            'authors' => ["John Doe <john@example.com>"],
            'title' => "Another test",
            'text' => [
                'value' => "<p>Hello, John!</p>",
                'format' => 'application/text+html',
            ],
        ];

        /** @var \MakinaCorpus\Normalizer\Tests\Functional\MockArticle $object */
        $object = $normalizer->denormalize('app.article', $data, new Context($map));

        $this->assertInstanceOf(MockArticle::class, $object);
        $this->assertSame((string)$id, $object->getId()->__toString());
        // @todo date $this->assertSame("Another test", $object->getTitle());
        $this->assertSame("Another test", $object->getTitle());
        $this->assertSame("<p>Hello, John!</p>", $object->getMarkup()->getText());
        $this->assertSame("application/text+html", $object->getMarkup()->getFormat());

        $values = $normalizer->normalize('app.article', $object, new Context($map));
        $this->assertSame($data, $values);
    }

    public function testNormalizeWithGroup()
    {
        
    }
}

/**
 * Simpler scenario: all optional scalars
 */
class MockTextWithFormat
{
    /** @var string */
    private $text;

    /**
     * @return null|string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /** @var string */
    private $format;

    /**
     * @return null|string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }
}

/**
 * Test hydration for internal arbitrary class
 */
class MockWithText
{
    /** @var ?string */
    private $title;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**@var ?MockTextWithFormat */
    private $text;

    public function getMarkup(): ?MockTextWithFormat
    {
        return $this->text;
    }
}

/**
 * Contains collections, mandatory properties, complex types
 */
final class MockArticle extends MockWithText
{
    /** @var UuidInterface */
    private $id;

    public function getId(): UuidInterface
    {
        return $this->id ?? ($this->id = Uuid::uuid4());
    }

    /** @var \DateTimeInterface */
    private $createdAt;

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt ?? ($this->createdAt = new \DateTimeImmutable());
    }

    /** @var \DateTimeInterface */
    private $updatedAt;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /** @var string[] */
    private $authors = [];

    public function getAuthors(): array
    {
        return $this->authors ?? [];
    }
}
