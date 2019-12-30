<?php declare(strict_types=1);

namespace App\ValueObject;

class Offset
{
    private int $offset;

    private function __construct(int $offset)
    {
        self::ensurePositiveNumber($offset);
        $this->offset = $offset;
    }

    public static function createFromInt(int $offset) : self
    {
        return new self($offset);
    }

    public static function createFromLimitAndPage(int $perPage, int $page) : self
    {
        return new self(($page - 1) * $perPage);
    }

    public static function createFromString(string $offset) : self
    {
        self::ensureStringContainsJustDigits($offset);

        return new self((int)$offset);
    }

    private static function ensurePositiveNumber(int $offset) : void
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException('Offset has to be greater than 0: ' . $offset);
        }
    }

    private static function ensureStringContainsJustDigits(string $offset) : void
    {
        if (ctype_digit($offset) === false) {
            throw new \InvalidArgumentException('Offset contains more than only digits: ' . $offset);
        }
    }

    public function __toString() : string
    {
        return (string)$this->offset;
    }

    public function asInt() : int
    {
        return $this->offset;
    }
}