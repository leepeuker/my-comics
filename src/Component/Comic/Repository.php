<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\PlainText;
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

    public function create(
        ?Id $comicVineId,
        ?Id $coverId,
        PlainText $name,
        ?Year $year,
        ?Id $publisherId,
        PlainText $description,
        ?DateTime $addedToCollection,
        ?Price $price
    ) : Entity {
        $this->dbConnection->insert(
            'comics', [
                'comic_vine_id' => $comicVineId === null ? null : $comicVineId->asInt(),
                'cover_id' => $coverId,
                'name' => $name,
                'year' => $year === null ? null : $year->asInt(),
                'publisher_id' => $publisherId === null ? null : $publisherId->asInt(),
                'description' => $description,
                'added_to_collection' => $addedToCollection === null ? null : (string)$addedToCollection,
                'price' => $price === null ? null : $price->asInt()
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
            'SELECT * FROM `comics` ORDER BY name'
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
                'added_to_collection' => $entity->getAddedToCollection(),
                'publisher_id' => $entity->getPublisherId(),
                'price' => $entity->getPrice()
            ],
            [
                'id' => $entity->getId()
            ]
        );
    }

    public function updateWithoutCover(
        Id $id,
        ?Id $comicVineId,
        PlainText $name,
        ?Year $year,
        PlainText $description,
        ?DateTime $addedToCollection,
        Id $publisherId,
        ?Price $price
    ) : void {
        $this->dbConnection->update(
            'comics',
            [
                'comic_vine_id' => $comicVineId,
                'name' => $name,
                'year' => $year,
                'description' => $description,
                'publisher_id' => $publisherId,
                'added_to_collection' => $addedToCollection,
                'price' => $price
            ],
            [
                'id' => $id
            ]
        );
    }
}