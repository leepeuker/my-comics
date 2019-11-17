<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\AbstractList;

class DtoList extends AbstractList
{
    public static function create() : self
    {
        return new self();
    }

    public function add(Dto $slug) : void
    {
        $this->data[] = $slug;
    }
}