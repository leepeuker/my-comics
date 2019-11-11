<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\ValueObject\Id;
use App\ValueObject\Price;
use App\ValueObject\Year;
use Doctrine\DBAL;

class Repository
{
    private DBAL\Connection $dbConnection;

    public function __construct(DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function create(Id $comicVineId, ?Id $coverId, string $name, ?Year $year, ?Id $publisherId, string $description, Price $price) : Entity
    {
        $this->dbConnection->insert(
            'comics', [
                'comic_vine_id' => $comicVineId->asInt(),
                'cover_id' => $coverId,
                'name' => $name,
                'year' => $year === null ? null : $year->asInt(),
                'publisher_id' => $publisherId === null ? null : $publisherId->asInt(),
                'description' => $description,
                'price' => $price->asInt()
            ]
        );

        return $this->fetchById(
            Id::createFromString($this->dbConnection->lastInsertId())
        );
    }

    public function deleteById(string $id) : void
    {
        $this->dbConnection->delete('comics', ['id' => $id,]);
    }

    public function fetchAll() : EntityList
    {
        $data = $this->dbConnection->fetchAll(
            'SELECT * FROM `comics`'
        );

        return EntityList::createFromArray($data);
    }

    public function fetchById(Id $id) : Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `comics` WHERE id = ?', [$id->asInt()]);

        if ($data === false) {
            throw new \RuntimeException('No comic found by id: ' . $id);
        }

        return Entity::createFromArray($data);
    }

    public function update(Entity $entity) : void
    {
        $this->dbConnection->update(
            'comics',
            [
                'comic_vine_id' => $entity->getComicVineId(),
                'cover_id' => $entity->getCoverId(),
                'name' => $entity->getName(),
                'year' => $entity->getYear(),
                'description' => $entity->getDescription(),
                'publisher_id' => $entity->getPublisherId(),
                'price' => $entity->getPrice()
            ],
            [
                'id' => $entity->getId()
            ]
        );
    }
}