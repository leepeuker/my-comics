<?php declare(strict_types=1);

namespace App\ValueObject;

class Id implements \JsonSerializable
{
    private int $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function createFromInt(int $id) : self
    {
        return new self($id);
    }

    public static function createFromString(string $id) : self
    {
        self::ensureStringContainsJustDigits($id);

        return new self((int)$id);
    }

    private static function ensureStringContainsJustDigits(string $id) : void
    {
        if (ctype_digit($id) === false) {
            throw new \InvalidArgumentException('Id contains more than digits: ' . $id);
        }
    }

    public function __toString() : string
    {
        return (string)$this->id;
    }

    public function asInt() : int
    {
        return $this->id;
    }

    public function jsonSerialize() : int
    {
        return $this->id;
    }
}