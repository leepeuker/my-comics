<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\Component\Comic\ValueObject\Search;
use App\Component\Image;
use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\Offset;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Rating;
use App\ValueObject\Year;

class Service
{
    private Image\Service $imageService;

    private Repository $repository;

    public function __construct(Repository $repository, Image\Service $imageService)
    {
        $this->repository = $repository;
        $this->imageService = $imageService;
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

    public function delete(Id $id) : void
    {
        $coverImageId = $this->imageService->findImageIdByComicId($id);

        $this->repository->deleteById($id);

        if ($coverImageId !== null) {
            $this->imageService->deleteById($coverImageId);
        }
    }

    public function fetchAll(Search $search) : EntityList
    {
        $offset = Offset::createFromLimitAndPage($search->getPerPage(), $search->getPage());

        return $this->repository->fetchAll($search->getPerPage(), $offset, $search->getSortBy(), $search->getSortOrder());
    }

    public function fetchAveragePrice() : int
    {
        return (int)round($this->repository->fetchTotalPrice() / $this->count());
    }

    public function fetchById(Id $id) : Entity
    {
        return $this->repository->fetchById($id);
    }

    public function fetchBySearchTerm(Search $search) : EntityList
    {
        $offset = Offset::createFromLimitAndPage($search->getPerPage(), $search->getPage());

        return $this->repository->fetchBySearchTerm($search->getTerm(), $search->getPerPage(), $offset, $search->getSortBy(), $search->getSortOrder());
    }

    public function fetchTotalPrice() : int
    {
        return $this->repository->fetchTotalPrice();
    }

    public function update(
        Id $comicId,
        ?Id $comicVineId,
        ?Id $coverImageId,
        PlainText $name,
        ?Year $year,
        PlainText $description,
        ?DateTime $addedToCollection,
        ?Id $publisherId,
        ?Price $price,
        ?Rating $rating
    ) : void {
        $oldCoverImageId = $this->imageService->findImageIdByComicId($comicId);

        $this->repository->update($comicId, $comicVineId, $coverImageId, $name, $year, $description, $addedToCollection, $publisherId, $price, $rating);

        if ($oldCoverImageId !== null && $oldCoverImageId->isEqual($coverImageId) === false) {
            $this->imageService->deleteById($oldCoverImageId);
        }
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