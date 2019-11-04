<?php declare(strict_types=1);

namespace App\Provider\ComicVine\Resource\Issue;

use App\ValueObject\Id;

class Volume
{
    private Id $id;

    private string $name;

    private function __construct(Id $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromInt((int)$data['id']),
            (string)$data['name']
        );
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }
}