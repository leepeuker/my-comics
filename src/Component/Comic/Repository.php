<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\Offset;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Rating;
use App\ValueObject\Year;
use Doctrine\DBAL;

class Repository
{
    private DBAL\Connection $dbConnection;

    public function __construct(DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function count() : int
    {
        $count = $this->dbConnection->fetchColumn('SELECT COUNT(ID) FROM `comics`');

        if ($count === false) {
            throw new \RuntimeException('Could not count comics.');
        }

        return (int)$count;
    }

    public function countBySearchTerm(?string $searchTerm) : int
    {
        $count = $this->dbConnection->fetchColumn(
            'SELECT COUNT(ID) FROM `comics` WHERE name LIKE ?',
            ["%$searchTerm%"]
        );

        if ($count === false) {
            throw new \RuntimeException('Could not count comics.');
        }

        return (int)$count;
    }

    public function create(
        ?Id $comicVineId,
        ?Id $coverId,
        PlainText $name,
        ?Year $year,
        ?Id $publisherId,
        PlainText $description,
        ?DateTime $addedToCollection,
        ?Price $price,
        ?Rating $rating
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
                'price' => $price === null ? null : $price->asInt(),
                'rating' => $rating === null ? null : $rating->asInt()
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

    public function fetchAll(int $perPage, Offset $offset) : EntityList
    {
        $data = $this->dbConnection->fetchAll(
            sprintf('SELECT * FROM `comics` ORDER BY name LIMIT %d OFFSET %d', $perPage, $offset->asInt())
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

    public function fetchBySearchTerm(string $searchTerm, int $perPage, Offset $offset) : EntityList
    {
        $data = $this->dbConnection->fetchAll(
            sprintf('SELECT * FROM `comics` WHERE name LIKE ? ORDER BY name LIMIT %d OFFSET %d', $perPage, $offset->asInt()),
            ["%$searchTerm%"]
        );

        return EntityList::createFromArray($data);
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
                'price' => $entity->getPrice(),
                'rating' => $entity->getRating()
            ],
            [
                'id' => $entity->getId()
            ]
        );
    }

    public function updateCover(Id $comicId, Id $coverId) : void
    {
        $this->dbConnection->update(
            'comics',
            [
                'cover_id' => $coverId,
            ],
            [
                'id' => $comicId
            ]
        );
    }

    public function updateWithoutCover(
        Id $comicId,
        ?Id $comicVineId,
        PlainText $name,
        ?Year $year,
        PlainText $description,
        ?DateTime $addedToCollection,
        ?Id $publisherId,
        ?Price $price,
        ?Rating $rating
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
                'price' => $price,
                'rating' => $rating
            ],
            [
                'id' => $comicId
            ]
        );
    }
}