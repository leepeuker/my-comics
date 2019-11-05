<?php declare(strict_types=1);

namespace App\Component\Publisher;

use App\ValueObject\Id;

class Entity implements \JsonSerializable
{
    private Id $comicVineId;

    private Id $id;

    private string $name;

    private function __construct(Id $id, Id $comicVineId, string $name)
    {
        $this->id = $id;
        $this->comicVineId = $comicVineId;
        $this->name = $name;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromString((string)$data['id']),
            Id::createFromString((string)$data['comic_vine_id']),
            (string)$data['name'],
        );
    }

    public function getComicVineId() : Id
    {
        return $this->comicVineId;
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'comicVineId' => $this->comicVineId,
            'name' => $this->name
        ];
    }
}