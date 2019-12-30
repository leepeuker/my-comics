<?php declare(strict_types=1);

namespace App\Component\Publisher;

use App\ValueObject\DateTime;
use App\ValueObject\Id;

class Entity implements \JsonSerializable
{
    private Id $comicVineId;

    private DateTime $createdAt;

    private Id $id;

    private string $name;

    private ?DateTime $updatedAt;

    private function __construct(Id $id, Id $comicVineId, string $name, DateTime $createdAt, ?DateTime $updatedAt)
    {
        $this->id = $id;
        $this->comicVineId = $comicVineId;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromString((string)$data['id']),
            Id::createFromString((string)$data['comic_vine_id']),
            (string)$data['name'],
            DateTime::createFromString((string)$data['created_at']),
            empty($data['updated_at']) === false ? DateTime::createFromString((string)$data['updated_at']) : null
        );
    }

    public function getComicVineId() : Id
    {
        return $this->comicVineId;
    }

    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'comicVineId' => $this->comicVineId,
            'name' => $this->name,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}