<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Year;

class Service
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
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
        return $this->repository->create($comicVineId, $coverId, $name, $year, $publisherId, $description, $addedToCollection, $price);
    }

    public function fetchAll() : EntityList
    {
        return $this->repository->fetchAll();
    }

    public function fetchById(Id $id) : Entity
    {
        return $this->repository->fetchById($id);
    }

    public function updateCover(Id $comicId, Id $coverId) : void
    {
        $this->repository->updateCover($comicId, $coverId);
    }

    public function updateWithoutCover(
        Id $comicId,
        ?Id $comicVineId,
        PlainText $name,
        ?Year $year,
        PlainText $description,
        ?DateTime $addedToCollection,
        ?Id $publisherId,
        ?Price $price
    ) : void {
        $this->repository->updateWithoutCover($comicId, $comicVineId, $name, $year, $description, $addedToCollection, $publisherId, $price);
    }
}