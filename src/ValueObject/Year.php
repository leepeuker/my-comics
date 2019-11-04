<?php declare(strict_types=1);

namespace App\ValueObject;

class Year
{
    private int $year;

    private function __construct(int $year)
    {
        $this->ensureValidRange($year);

        $this->year = $year;
    }

    public static function createFromInt(int $year) : self
    {
        return new self($year);
    }

    public function __toString() : string
    {
        return (string)$this->year;
    }

    public function asInt() : int
    {
        return $this->year;
    }

    private function ensureValidRange(int $year) : void
    {
        if ($year < 1901 || $year > 2155) {
            throw new \InvalidArgumentException('Year has to be between 1901 and 2155, invalid value: ' . $year);
        }
    }
}