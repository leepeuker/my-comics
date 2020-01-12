<?php declare(strict_types=1);

namespace App\ValueObject\Query;

class SortOrder
{
    private const VALID_ORDERS = [
        'asc',
        'desc'
    ];

    private string $order;

    private function __construct(string $order)
    {
        $this->order = $order;
    }

    public static function create(string $order) : self
    {
        self::ensureValidValue($order);

        return new self($order);
    }

    private static function ensureValidValue(string $order) : void
    {
        if (in_array($order, self::VALID_ORDERS, true) === false) {
            throw new \RuntimeException('Invalid sort order value: ' . $order);
        }
    }

    public function __toString() : string
    {
        return $this->order;
    }
}