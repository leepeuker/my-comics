<?php declare(strict_types=1);

namespace App\Component\Comic\ValueObject;

use App\ValueObject\Query\SortOrder;

class Search
{
    private const DEFAULT_SORT_BY = 'name';
    private const DEFAULT_SORT_ORDER = 'asc';
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;

    private string $sortBy;

    private SortOrder $sortOrder;

    private int $page;

    private int $perPage;

    private string $term;

    private function __construct(?string $term, ?int $page, ?int $perPage, ?string $sortBy, ?SortOrder $sortOrder)
    {
        $this->term = $term ?? '';
        $this->page = $page ?? self::DEFAULT_PAGE;
        $this->perPage = $perPage ?? self::DEFAULT_PER_PAGE;
        $this->sortBy = $sortBy ?? self::DEFAULT_SORT_BY;
        $this->sortOrder = $sortOrder ?? SortOrder::create(self::DEFAULT_SORT_ORDER);
    }

    public static function create(?string $term, ?int $page, ?int $perPage, ?string $sortBy, ?SortOrder $sortOrder) : self
    {
        return new self($term, $page, $perPage, $sortBy, $sortOrder);
    }

    public function getSortBy() : string
    {
        return $this->sortBy;
    }

    public function getSortOrder() : SortOrder
    {
        return $this->sortOrder;
    }

    public function getPage() : int
    {
        return $this->page;
    }

    public function getPerPage() : int
    {
        return $this->perPage;
    }

    public function getTerm() : string
    {
        return $this->term;
    }
}