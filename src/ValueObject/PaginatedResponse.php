<?php declare(strict_types=1);

namespace App\ValueObject;

class PaginatedResponse implements \JsonSerializable
{
    private \JsonSerializable $items;

    private int $page;

    private int $perPage;

    private int $totalItems;

    private function __construct(\JsonSerializable $items, int $totalItems, int $perPage, int $page)
    {
        $this->items = $items;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->totalItems = $totalItems;
    }

    public static function createFromParameters(\JsonSerializable $items, int $totalItems, int $perPage, int $page) : self
    {
        return new self($items, $totalItems, $perPage, $page);
    }

    public function jsonSerialize() : array
    {
        return [
            'items' => $this->items,
            'totalItems' => $this->totalItems,
            'perPage' => $this->perPage,
            'page' => $this->page
        ];
    }
}