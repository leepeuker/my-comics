<?php declare(strict_types=1);

namespace App\Component\Publisher;

use App\ValueObject\Id;
use Doctrine\DBAL;

class Repository
{
    private DBAL\Connection $dbConnection;

    public function __construct(DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function create(?Id $comicVineId, string $name) : Entity
    {
        $this->dbConnection->insert(
            'publishers', [
                'comic_vine_id' => $comicVineId,
                'name' => $name
            ]
        );

        $id = Id::createFromString($this->dbConnection->lastInsertId());

        return $this->fetchById($id);
    }

    public function deleteById(string $id) : void
    {
        $this->dbConnection->delete('publishers', ['id' => $id]);
    }

    public function fetchAll() : EntityList
    {
        $data = $this->dbConnection->fetchAll('SELECT * FROM `publishers` ORDER BY name');

        return EntityList::createFromArray($data);
    }

    public function fetchByComicVineId(Id $comicVineId) : ?Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `publishers` WHERE comic_vine_id = ?', [$comicVineId->asInt()]);

        if ($data === false) {
            return null;
        }

        return Entity::createFromArray($data);
    }

    public function fetchById(Id $id) : Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `publishers` WHERE id = ?', [$id->asInt()]);

        if ($data === false) {
            throw new \RuntimeException('No publisher found by id: ' . $id);
        }

        return Entity::createFromArray($data);
    }

    public function fetchByName(string $name) : ?Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `publishers` WHERE name = ?', [$name]);

        if ($data === false) {
            return null;
        }

        return Entity::createFromArray($data);
    }

    public function fetchPublishersComicCost() : array
    {
        return $this->dbConnection->fetchAll(
            'SELECT publishers.name, SUM(c.price) AS cost
                FROM publishers
                INNER JOIN comics c on publishers.id = c.publisher_id
                GROUP BY publishers.id
                ORDER BY SUM(c.price) DESC ;'
        );
    }

    public function fetchPublishersComicCount() : array
    {
        return $this->dbConnection->fetchAll(
            'SELECT publishers.name, COUNT(c.id) AS count
                FROM publishers
                INNER JOIN comics c on publishers.id = c.publisher_id
                GROUP BY publishers.id
                ORDER BY COUNT(c.id) DESC ;'
        );
    }

    public function update(Entity $entity) : void
    {
        $this->dbConnection->update(
            'publishers',
            [
                'comic_vine_id' => $entity->getComicVineId(),
                'name' => $entity->getName(),
            ],
            [
                'id' => $entity->getId()
            ]
        );
    }
}