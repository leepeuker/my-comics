<?php declare(strict_types=1);

namespace App\Component\Image;

use App\Exception\ResourceNotFound;
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

    public function deleteById(Id $id) : void
    {
        $this->dbConnection->delete('images', ['id' => $id->asInt(),]);
    }

    public function fetchAllNames() : array
    {
        $stmt = $this->dbConnection->prepare('SELECT file_name FROM `images`');
        $stmt->execute();

        return $stmt->fetchAll(DBAL\FetchMode::COLUMN);
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
            throw new ResourceNotFound('No image found by id: ' . $id);
        }

        return Entity::createFromArray($data);
    }

    public function findByComicId(Id $comicId) : ?Entity
    {
        $data = $this->dbConnection->fetchAssoc(
            'SELECT `images`.*
            FROM `images`
            JOIN `comics` on `images`.id = `comics`.cover_id
            WHERE `comics`.id = ?',
            [$comicId->asInt()]
        );

        return $data === false ? null : Entity::createFromArray($data);
    }

    public function findById(Id $id) : ?Entity
    {
        try {
            return $this->fetchById($id);
        } catch (ResourceNotFound $e) {
            return null;
        }
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