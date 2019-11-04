<?php declare(strict_types=1);

namespace App\Component\Image;

use App\ValueObject\Id;

class Entity
{
    private string $fileName;

    private Id $id;

    private function __construct(Id $id, string $fileName)
    {
        $this->id = $id;
        $this->fileName = $fileName;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromString((string)$data['id']),
            (string)$data['file_name'],
        );
    }

    public static function createFromParameters(Id $id, string $name) : self
    {
        return new self($id, $name);
    }

    public function getFileName() : string
    {
        return $this->fileName;
    }

    public function getId() : Id
    {
        return $this->id;
    }
}