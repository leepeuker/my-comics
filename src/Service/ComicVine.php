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

    private Component\Image\Repository $imageRepository;

    private Component\Issue\Repository $issueRepository;

    private Component\Publisher\Repository $publisherRepository;

    private Component\Volume\Repository $volumeRepository;

    public function __construct(
        Api $api,
        Component\Volume\Repository $volumeRepository,
        Component\Publisher\Repository $publisherRepository,
        Component\Issue\Repository $issueRepository,
        Component\Image\Repository $imageRepository
    ) {
        $this->api = $api;
        $this->volumeRepository = $volumeRepository;
        $this->publisherRepository = $publisherRepository;
        $this->issueRepository = $issueRepository;
        $this->imageRepository = $imageRepository;
    }

    public function createComicByIssueId(Id $issueId) : Component\Issue\Entity
    {
        $comicVineIssue = $this->api->fetchIssue($issueId);
        $comicVineVolume = $this->api->fetchVolume($comicVineIssue->getVolume()->getId());

        return $this->persist($comicVineIssue, $comicVineVolume);
    }

    public function getCover(Issue\Dto $comicVineIssue) : Component\Image\Entity
    {
        try {
            return $this->imageRepository->fetchByFileName(
                $comicVineIssue->getCoverUrl()
            );
        } catch (RuntimeException $exception) {
            return $this->imageRepository->create(
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

    public function getVolume(Issue\Dto $comicVineIssue) : Component\Volume\Entity
    {
        try {
            return $this->volumeRepository->fetchByComicVineId(
                $comicVineIssue->getVolume()->getId()
            );
        } catch (RuntimeException $exception) {
            return $this->volumeRepository->create(
                $comicVineIssue->getVolume()->getId(),
                $this->convertString($comicVineIssue->getVolume()->getName())
            );
        }
    }

    public function persist(Issue\Dto $comicVineIssue, Volume\Dto $comicVineVolume) : Component\Issue\Entity
    {
        $publisher = $this->getPublisher($comicVineVolume);
        $volume = $this->getVolume($comicVineIssue);
        $cover = $this->getCover($comicVineIssue);

        return $this->issueRepository->create(
            $comicVineIssue->getId(),
            $cover->getId(),
            $this->convertString($comicVineIssue->getName()),
            $this->getYear($comicVineIssue),
            $volume->getId(),
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