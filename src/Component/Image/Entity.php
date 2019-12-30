<?php declare(strict_types=1);

namespace App\Component\Image;

use App\ValueObject\DateTime;
use App\ValueObject\Id;

class Entity implements \JsonSerializable
{
    private DateTime $createdAt;

    private string $fileName;

    private Id $id;

    private ?DateTime $updatedAt;

    private function __construct(Id $id, string $fileName, DateTime $createdAt, ?DateTime $updatedAt)
    {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromString((string)$data['id']),
            (string)$data['file_name'],
            DateTime::createFromString((string)$data['created_at']),
            empty($data['updated_at']) === false ? DateTime::createFromString((string)$data['updated_at']) : null
        );
    }

    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    public function getFileName() : string
    {
        return $this->fileName;
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'fileName' => $this->fileName,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}