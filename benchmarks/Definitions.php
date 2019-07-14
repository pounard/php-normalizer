<?php
/**
 * Defines mock types for benchmarking.
 *
 * So far, you have to take into account that:
 *
 *  - Symfony normalizer requires to have setters or constructor arguments
 *    for hydrating objects, this is due to symfony/property-access usage,
 *    hence we defined all in those classes, even though in real life use
 *    case you might want to have immutable objects instead.
 *
 *  - As of now, we require to have all our types to have their complete
 *    description in the YAML file.
 */

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Simpler scenario: all optional scalars
 */
class MockTextWithFormat
{
    /** @var string */
    private $text;

    public function getText(): ?string
    {
        return $this->text;
    }

    /** @var string */
    private $format;

    public function getFormat(): ?string
    {
        return $this->format;
    }
}

/**
 * Test hydration for internal arbitrary class
 */
class MockWithTitle
{
    /** @var string */
    private $title;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $value): void
    {
        $this->title = $value;
    }
}

/**
 * Test hydration for internal arbitrary class
 *
 * Strict typing is temporarily disabled because Symfony property info component
 * is missing properties from parent classes, it is unable to find the dockblock
 * for the "text" property, hence it is unable to correcly guess the type.
 */
class MockWithText extends MockWithTitle
{
    /** @var MockTextWithFormat */
    private $text;

    /** @return null|MockTextWithFormat */
    public function getMarkup()/*: ?MockTextWithFormat */
    {
        return $this->text;
    }

    public function setText(/* ?MockTextWithFormat */ $value): void
    {
        $this->text = $value;
    }
}

trait LotsOfProperties
{
    private $foo;

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo(/* ?\UuidInterface */ $value): void
    {
        $this->foo = $value;
    }

    private $bar;

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar(/* ?\UuidInterface */ $value): void
    {
        $this->bar = $value;
    }

    private $baz;

    public function getBaz()
    {
        return $this->baz;
    }

    public function setBaz(/* ?\UuidInterface */ $value): void
    {
        $this->baz = $value;
    }

    private $filename;

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(/* ?string */ $value): void
    {
        $this->filename = $value;
    }
}

/**
 * Bus message alike data structure
 */
final class AddToCartMessage
{
    /** @var int */
    private $orderId;

    /** @var int */
    private $productId;

    /** @var int */
    private $amount;

    public function __construct(int $orderId, int $productId, int $amount = 1)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->amount = $amount;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}

/**
 * Contains collections, mandatory properties, complex types
 */
final class MockArticle extends MockWithText
{
    use LotsOfProperties;

    /** @var \Ramsey\Uuid\UuidInterface */
    private $id;

    public function getId(): UuidInterface
    {
        return $this->id ?? ($this->id = Uuid::uuid4());
    }

    public function setId(/* ?\UuidInterface */ $value): void
    {
        $this->id = $value;
    }

    /** @var \DateTimeInterface */
    private $createdAt;

    /** @return \DateTimeInterface */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt ?? ($this->createdAt = new \DateTimeImmutable());
    }

    public function setCreatedAt(/* ?\DateTimeInterface */ $value): void
    {
        $this->createdAt = $value;
    }

    /** @var \DateTimeInterface */
    private $updatedAt;

    /** @return \DateTimeInterface */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdateddAt(/* ?\DateTimeInterface */ $value): void
    {
        $this->updatedAt = $value;
    }

    /** @var string[] */
    private $authors = [];

    public function getAuthors(): array
    {
        return $this->authors ?? [];
    }

    public function setAuthors(/* ?array */ $value): void
    {
        $this->authors = $value;
    }
}
