<?php declare(strict_types=1);

namespace App\Component\Image;

use App\ValueObject\Id;
use Doctrine\DBAL;

class Repository
{
    private DBAL\Connection $dbConnection;

    public function __construct(DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function create(string $fileName) : Entity
    {
        $this->dbConnection->insert(
            'images', [
                'file_name' => $fileName,
            ]
        );

        return $this->fetchById(
            Id::createFromString($this->dbConnection->lastInsertId())
        );
    }

    public function deleteById(string $id) : void
    {
        $this->dbConnection->delete('images', ['id' => $id,]);
    }

    public function fetchByFileName(string $fileName) : ?Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `images` WHERE file_name = ?', [$fileName]);

        if ($data === false) {
            return null;
        }

        return Entity::createFromArray($data);
    }

    public function fetchById(Id $id) : Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `images` WHERE id = ?', [$id->asInt()]);

        if ($data === false) {
            throw new \RuntimeException('No image found by id: ' . $id);
        }

        return Entity::createFromArray($data);
    }

    public function update(Entity $entity) : void
    {
        $this->dbConnection->update(
            'images',
            [
                'file_name' => $entity->getFileName(),
            ],
            [
                'id' => $entity->getId()
            ]
        );
    }
}