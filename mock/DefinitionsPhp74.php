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

namespace MakinaCorpus\Normalizer\Mock;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Generate objects.
 */
final class Php74ObjectGenerator
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

        $hydrator1 = \Closure::bind(static function (Php74MockArticle $object) use ($faker) {
            $object->id = Uuid::uuid4();
            $object->createdAt = $faker->dateTimeThisCentury;
            $object->updatedAt = $faker->dateTimeThisCentury;
            $object->authors = Php74ObjectGenerator::generateNameList($faker);
            $object->foo = $faker->jobTitle;
            $object->bar = $faker->randomDigitNotNull;
            $object->baz = $faker->randomFloat();
            $object->filename = $faker->freeEmail;
        }, null, Php74MockArticle::class);

        $hydrator2 = \Closure::bind(static function (Php74MockWithTitle $object) use ($faker) {
            $object->title = $faker->sentence;
        }, null, Php74MockWithTitle::class);

        $hydrator3 = \Closure::bind(static function (Php74MockWithText $object) use ($faker) {
            $object->text = new Php74MockTextWithFormat($faker->text, 'application/text+html');
        }, null, Php74MockWithText::class);

        $ret = new Php74MockArticle();
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

        return new Php74AddToCartMessage(Uuid::uuid4(), $faker->randomDigitNotNull, $faker->randomDigit);
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
class Php74MockTextWithFormat
{
    private ?string $text;

    public function getText(): ?string
    {
        return $this->text;
    }

    private string $format;

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function __construct(?string $text = null, ?string $format = null)
    {
        $this->text = $text;
        $this->format = $format ?? 'html';
    }
}

/**
 * Test hydration for internal arbitrary class
 */
class Php74MockWithTitle
{
    private string $title;

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
class Php74MockWithText extends Php74MockWithTitle
{
    /** @var null|Php74MockTextWithFormat */
    private ?Php74MockTextWithFormat $text;

    /** @return null|Php74MockTextWithFormat */
    public function getMarkup(): ?Php74MockTextWithFormat
    {
        return $this->text;
    }

    /**
     * Fun thing, without the type hint here, because symfony/serializer
     * attempt to normalize based upon setter injection instead of property
     * injection, this cannot work and will raise exception.
     *
     * This is silent in the benchmarks targetting previous PHP versions
     * because PHP wasn't raising TypeError exceptions, but now we see them:
     * Symfony's serializer is very fragile and silently give invalid data.
     *
     * As soon as we properly typed setters, Syfmony's normalizer had a more
     * than 2x performance penalty. It is extremely slow, with or without
     * metadata cache.
     */
    public function setText(?Php74MockTextWithFormat $value): void
    {
        $this->text = $value;
    }
}

trait Php74LotsOfProperties
{
    private ?string $foo;

    public function getFoo(): ?string
    {
        return $this->foo;
    }

    public function setFoo(?string $value): void
    {
        $this->foo = $value;
    }

    private int $bar;

    public function getBar(): int
    {
        return $this->bar;
    }

    public function setBar(int $value): void
    {
        $this->bar = $value;
    }

    private float $baz;

    public function getBaz(): float
    {
        return $this->baz;
    }

    public function setBaz(float $value): void
    {
        $this->baz = $value;
    }

    private string $filename;

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
final class Php74AddToCartMessage
{
    private ?UuidInterface $orderId;
    private int $productId;
    private float $amount;

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
final class Php74MockArticle extends Php74MockWithText
{
    use Php74LotsOfProperties;

    private ?UuidInterface $id = null;

    private \DateTimeImmutable $createdAt;

    private ?\DateTimeImmutable $updatedAt;

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $value): void
    {
        $this->updatedAt = $value;
    }

    /** @var string[] */
    private array $authors = [];

    public function getAuthors(): array
    {
        return $this->authors ?? [];
    }

    public function setAuthors(/* ?array */ $value): void
    {
        $this->authors = $value;
    }
}
