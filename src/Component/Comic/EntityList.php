<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\AbstractList;

class EntityList extends AbstractList
{
    public static function create() : self
    {
        return new self();
    }

    public static function createFromArray(array $data) : self
    {
        $list = self::create();

        foreach ($data as $entity) {
            $list->add(Entity::createFromArray((array)$entity));
        }

        return $list;
    }

    public function add(Entity $slug) : void
    {
        $this->data[] = $slug;
    }
}