<?php declare(strict_types=1);

namespace App\ValueObject;

class Rating implements \JsonSerializable
{
    private const MAX_RATING = 3;
    private const MIN_RATING = 1;

    private int $rating;

    private function __construct(int $rating)
    {
        self::ensureValidValue($rating);

        $this->rating = $rating;
    }

    public static function createFromInt(int $rating) : self
    {
        return new self($rating);
    }

    public static function createFromString(string $rating) : self
    {
        self::ensureStringContainsJustDigits($rating);

        return new self((int)$rating);
    }

    private static function ensureStringContainsJustDigits(string $rating) : void
    {
        if (ctype_digit($rating) === false) {
            throw new \InvalidArgumentException('Rating contains more than digits: ' . $rating);
        }
    }

    private static function ensureValidValue(int $rating) : void
    {
        if ($rating < self::MIN_RATING || $rating > self::MAX_RATING) {
            throw new \DomainException('Rating not in valid range: ' . $rating);
        }
    }

    public function __toString() : string
    {
        return (string)$this->rating;
    }

    public function asInt() : int
    {
        return $this->rating;
    }

    public function jsonSerialize() : int
    {
        return $this->rating;
    }
}