<?php declare(strict_types=1);

namespace App\Component\Comic\ValueObject;

use App\ValueObject\Query\SortOrder;
use Symfony\Component\HttpFoundation\Request;

class Search
{
    private const DEFAULT_SORT_BY = 'added_to_collection';
    private const DEFAULT_SORT_ORDER = 'desc';
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;

    private int $page;

    private int $perPage;

    private string $sortBy;

    private SortOrder $sortOrder;

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

    public static function createFromResponse(Request $request) : self
    {
        return new self(
            $request->query->has('term') === true ? (string)$request->query->get('term') : null,
            $request->query->has('page') === true ? $request->query->getInt('page') : null,
            $request->query->has('per_page') === true ? $request->query->getInt('per_page') : null,
            $request->query->has('sort_by') === true ? (string)$request->query->get('sort_by') : null,
            $request->query->has('sort_order') === true ? SortOrder::create($request->query->getAlnum('sort_order')) : null,
        );
    }

    public function getPage() : int
    {
        return $this->page;
    }

    public function getPerPage() : int
    {
        return $this->perPage;
    }

    public function getSortBy() : string
    {
        return $this->sortBy;
    }

    public function getSortOrder() : SortOrder
    {
        return $this->sortOrder;
    }

    public function getTerm() : string
    {
        return $this->term;
    }
}