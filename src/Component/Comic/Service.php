<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\Offset;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Rating;
use App\ValueObject\Year;

class Service
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function count(?string $searchTerm = null) : int
    {
        if ($searchTerm === null) {
            return $this->repository->count();
        }

        return $this->repository->countBySearchTerm($searchTerm);
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
        return $this->repository->create($comicVineId, $coverId, $name, $year, $publisherId, $description, $addedToCollection, $price, $rating);
    }

    public function fetchAll(int $perPage, int $page = 1) : EntityList
    {
        $offset = Offset::createFromLimitAndPage($perPage, $page);

        return $this->repository->fetchAll($perPage, $offset);
    }

    public function fetchById(Id $id) : Entity
    {
        return $this->repository->fetchById($id);
    }

    public function fetchBySearchTerm(string $searchTerm, int $perPage, int $page = 1) : EntityList
    {
        $offset = Offset::createFromLimitAndPage($perPage, $page);

        return $this->repository->fetchBySearchTerm($searchTerm, $perPage, $offset);
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
        ?Price $price,
        ?Rating $rating
    ) : void {
        $this->repository->updateWithoutCover($comicId, $comicVineId, $name, $year, $description, $addedToCollection, $publisherId, $price, $rating);
    }
}