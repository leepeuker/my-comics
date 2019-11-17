<?php declare(strict_types=1);

namespace App\Service;

use App\Component;
use App\Provider\ComicVine\Api;
use App\Provider\ComicVine\Resource\Issue;
use App\Provider\ComicVine\Resource\Volume;
use App\ValueObject\Id;
use App\ValueObject\Price;
use App\ValueObject\Year;
use RuntimeException;

class ComicVine
{
    private Api $api;

    private Component\Image\Service $imageService;

    private Component\Comic\Repository $comicRepository;

    private Component\Publisher\Repository $publisherRepository;

    public function __construct(
        Api $api,
        Component\Publisher\Repository $publisherRepository,
        Component\Comic\Repository $comicRepository,
        Component\Image\Service $imageService
    ) {
        $this->api = $api;
        $this->publisherRepository = $publisherRepository;
        $this->comicRepository = $comicRepository;
        $this->imageService = $imageService;
    }

    public function createComicByIssueId(Id $issueId) : Component\Comic\Entity
    {
        $comicVineIssue = $this->api->fetchIssue($issueId);
        $comicVineVolume = $this->api->fetchVolume($comicVineIssue->getVolume()->getId());

        return $this->persist($comicVineIssue, $comicVineVolume);
    }

    public function getCover(Issue\Dto $comicVineIssue) : Component\Image\Entity
    {
        try {
            return $this->imageService->fetchByFileName(
                $comicVineIssue->getCoverUrl()
            );
        } catch (RuntimeException $exception) {
            return $this->imageService->createFromUrl(
                $comicVineIssue->getCoverUrl()
            );
        }
    }

    public function getPublisher(Volume\Dto $comicVineVolume) : Component\Publisher\Entity
    {
        try {
            return $this->publisherRepository->fetchByComicVineId(
                $comicVineVolume->getPublisher()->getId()
            );
        } catch (RuntimeException $exception) {
            return $this->publisherRepository->create(
                $comicVineVolume->getPublisher()->getId(),
                $comicVineVolume->getPublisher()->getName()
            );
        }
    }

    public function persist(Issue\Dto $comicVineIssue, Volume\Dto $comicVineVolume) : Component\Comic\Entity
    {
        $publisher = $this->getPublisher($comicVineVolume);
        $cover = $this->getCover($comicVineIssue);

        return $this->comicRepository->create(
            $comicVineIssue->getId(),
            $cover->getId(),
            $this->convertString($comicVineVolume->getName() . ' - ' . $comicVineIssue->getName()),
            $this->getYear($comicVineIssue),
            $publisher->getId(),
            $this->convertString($comicVineIssue->getDescription()),
            Price::createFromInt(100)
        );
    }

    private function convertString(string $string) : string
    {
        return str_replace('’', '\'', $string);
    }

    private function getYear(Issue\Dto $comicVineIssue) : ?Year
    {
        $storeDate = $comicVineIssue->getStoreDate();

        return $storeDate === null ? null : Year::createFromInt((int)substr($storeDate, 0, 4));
    }
}