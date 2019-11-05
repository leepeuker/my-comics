<?php declare(strict_types=1);

namespace App\Component\Issue;

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

    public function create(Id $comicVineId, ?Id $coverId, string $name, ?Year $year, ?Id $volumeId, ?Id $publisherId, string $description, Price $price) : Entity
    {
        $this->dbConnection->insert(
            'issues', [
                'comic_vine_id' => $comicVineId->asInt(),
                'cover_id' => $coverId,
                'name' => $name,
                'year' => $year === null ? null : $year->asInt(),
                'volume_id' => $volumeId === null ? null : $volumeId->asInt(),
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
        $this->dbConnection->delete('issues', ['id' => $id,]);
    }

    public function fetchAll() : EntityList
    {
        $data = $this->dbConnection->fetchAll('SELECT * FROM `issues`');

        return EntityList::createFromArray($data);
    }

    public function fetchById(Id $id) : Entity
    {
        $data = $this->dbConnection->fetchAssoc('SELECT * FROM `issues` WHERE id = ?', [$id->asInt()]);

        if ($data === false) {
            throw new \RuntimeException('No issue found by id: ' . $id);
        }

        return Entity::createFromArray($data);
    }

    public function update(Entity $entity) : void
    {
        $this->dbConnection->update(
            'issues',
            [
                'comic_vine_id' => $entity->getComicVineId(),
                'cover_id' => $entity->getCoverId(),
                'name' => $entity->getName(),
                'year' => $entity->getYear(),
                'description' => $entity->getDescription(),
                'volume_id' => $entity->getVolumeId(),
                'publisher_id' => $entity->getPublisherId(),
                'price' => $entity->getPrice()
            ],
            [
                'id' => $entity->getId()
            ]
        );
    }
}