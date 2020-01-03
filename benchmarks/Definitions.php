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
 * Generate objects.
 */
final class ObjectGenerator
{
    /**
     * Create arbitrary name string list
     */
    public static function generateNameList(?int $size = null): array
    {
        $ret = [];

        $faker = \Faker\Factory::create();
        $size = $size ?? \rand(0,5);

        for ($i = 0; $i < $size; $i++) {
            $ret[] = $faker->name;
        }

        return $ret;
    }

    /**
     * Create single article normalized data
     */
    public static function createNormalizedArticle(bool $withId = true): array
    {
        $faker = \Faker\Factory::create();

        return [
            'id' => $withId ? (string)Uuid::uuid4() : null,
            'createdAt' => (string)($faker->dateTimeThisCentury)->format(\DateTime::ISO8601),
            'updatedAt' => (string)($faker->dateTimeThisCentury)->format(\DateTime::ISO8601),
            'authors' => self::generateNameList(),
            'title' => $faker->sentence,
            'text' => [
                'text' => $faker->text,
                'format' => 'application/text+html',
            ],
            'foo' => $faker->jobTitle,
            'bar' => $faker->randomDigitNotNull,
            'baz' => $faker->randomFloat(),
            'filename' => $faker->freeEmail,
        ];
    }

    /**
     * Create normalized article data list
     */
    public static function createNormalizedArticleList(int $count = 5, bool $withId = true): array
    {
        $ret = [];

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = self::createNormalizedArticle($withId);
        }

        return $ret;
    }

    /**
     * Create single article instance
     */
    public static function createInstanceArticle(bool $withId = true): array
    {
        $faker = \Faker\Factory::create();

        $hydrator1 = \Closure::bind(static function (MockArticle $object) use ($faker) {
            $object->id = Uuid::uuid4();
            $object->createdAt = $faker->dateTimeThisCentury;
            $object->updatedAt = $faker->dateTimeThisCentury;
            $object->authors = ObjectGenerator::generateNameList($faker);
            $object->foo = $faker->jobTitle;
            $object->bar = $faker->randomDigitNotNull;
            $object->baz = $faker->randomFloat();
            $object->filename = $faker->freeEmail;
        }, null, MockArticle::class);

        $hydrator2 = \Closure::bind(static function (MockWithTitle $object) use ($faker) {
            $object->title = $faker->sentence;
        }, null, MockWithTitle::class);

        $hydrator3 = \Closure::bind(static function (MockWithText $object) use ($faker) {
            $object->text = new MockTextWithFormat($faker->text, 'application/text+html');
        }, null, MockWithText::class);

        $ret = new MockArticle();
        \call_user_func($hydrator1, $ret);
        \call_user_func($hydrator2, $ret);
        \call_user_func($hydrator3, $ret);

        return $ret;
    }

    /**
     * Create article instance list
     */
    public static function createInstanceArticleList(int $count = 5, bool $withId = true): array
    {
        $ret = [];

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = self::createInstanceArticle($withId);
        }

        return $ret;
    }

    /**
     * Create single message normalized data
     */
    public static function createNormalizedMessage(bool $withId = true): array
    {
        $faker = \Faker\Factory::create();

        return [
            'orderId' => (string)Uuid::uuid4(),
            'productId' => $faker->randomDigitNotNull,
            'amount' => $faker->randomFloat(),
        ];
    }

    /**
     * Create normalized message data list
     */
    public static function createNormalizedMessageList(int $count = 5)
    {
        $ret = [];

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = self::createNormalizedMessage();
        }

        return $ret;
    }

    /**
     * Create single article instance
     */
    public static function createInstanceMessage(): array
    {
        $faker = \Faker\Factory::create();

        return new AddToCartMessage(Uuid::uuid4(), $faker->randomDigitNotNull, $faker->randomDigit);
    }

    /**
     * Create arbitrary data
     */
    public static function createInstanceMessageList(int $count = 5)
    {
        $ret = [];

        for ($i = 0; $i < $count; ++$i) {
            $ret[] = self::createInstanceMessage();
        }

        return $ret;
    }
}

/**
 * Simpler scenario: all optional scalars
 */
class MockTextWithFormat
{
    /** @var ?string */
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

    public function __construct(?string $text = null, ?string $format = null)
    {
        $this->text = $text;
        $this->format = $format;
    }
}

/**
 * Test hydration for internal arbitrary class
 */
class MockWithTitle
{
    /** @var string */
    private $title;

    public function getTitle(): string
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
    /** @var ?MockTextWithFormat */
    private $text;

    /** @return null|MockTextWithFormat */
    public function getMarkup(): ?MockTextWithFormat
    {
        return $this->text;
    }

    public function setText(?MockTextWithFormat $value): void
    {
        $this->text = $value;
    }
}

trait LotsOfProperties
{
    /** @var ?string */
    private $foo;

    public function getFoo(): ?string
    {
        return $this->foo;
    }

    public function setFoo(?string $value): void
    {
        $this->foo = $value;
    }

    /** @var int */
    private $bar;

    public function getBar(): int
    {
        return $this->bar;
    }

    public function setBar(int $value): void
    {
        $this->bar = $value;
    }

    /** @var float */
    private $baz;

    public function getBaz(): float
    {
        return $this->baz;
    }

    public function setBaz(float $value): void
    {
        $this->baz = $value;
    }

    /** @var string */
    private $filename;

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $value): void
    {
        $this->filename = $value;
    }
}

/**
 * Bus message alike data structure
 */
final class AddToCartMessage
{
    /** @var ?UuidInterface */
    private $orderId;

    /** @var int */
    private $productId;

    /** @var float */
    private $amount;

    public function __construct(UuidInterface $orderId, int $productId, float $amount = 1.0)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->amount = $amount;
    }

    public function getOrderId(): ?UuidInterface
    {
        return $this->orderId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getAmount(): float
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

    /** @var null|UuidInterface */
    private $id = null;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var ?\DateTimeImmutable */
    private $updatedAt;

    /** @return ?\DateTimeImmutable */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdateddAt(?\DateTimeImmutable $value): void
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
