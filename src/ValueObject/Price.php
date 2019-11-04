<?php declare(strict_types=1);

namespace App\ValueObject;

class Price
{
    private int $centAmount;

    private function __construct(int $centAmount)
    {
        $this->centAmount = $centAmount;
    }

    public static function createFromInt(int $centAmount) : self
    {
        return new self($centAmount);
    }

    public static function createFromString(string $centAmount) : self
    {
        self::ensureStringContainsJustDigits($centAmount);

        return new self((int)$centAmount);
    }

    private static function ensureStringContainsJustDigits(string $centAmount) : void
    {
        if (ctype_digit($centAmount) === false) {
            throw new \InvalidArgumentException('Price contains more than digits: ' . $centAmount);
        }
    }

    public function __toString() : string
    {
        return (string)$this->centAmount;
    }

    public function asInt() : int
    {
        return $this->centAmount;
    }
}