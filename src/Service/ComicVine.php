<?php declare(strict_types=1);

namespace App\Service;

use App\Component;
use App\Provider\ComicVine\Api;
use App\Provider\ComicVine\Resource\Issue;
use App\Provider\ComicVine\Resource\Volume;
use App\ValueObject\Id;
use App\ValueObject\PlainText;
use App\ValueObject\Year;

class ComicVine
{
    private Api $api;

    private Component\Comic\Service $comicService;

    private Component\Image\Service $imageService;

    private Component\Publisher\Service $publisherService;

    public function __construct(
        Api $api,
        Component\Publisher\Service $publisherService,
        Component\Comic\Service $comicService,
        Component\Image\Service $imageService
    ) {
        $this->api = $api;
        $this->publisherService = $publisherService;
        $this->comicService = $comicService;
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
        $image = $this->imageService->fetchByFileName(
            $comicVineIssue->getCoverUrl()
        );

        if ($image instanceof Component\Image\Entity) {
            return $image;
        }

        return $this->imageService->createFromUrl(
            $comicVineIssue->getCoverUrl()
        );
    }

    public function getPublisher(Volume\Dto $comicVineVolume) : Component\Publisher\Entity
    {
        $publisher = $this->publisherService->fetchByName($comicVineVolume->getPublisher()->getName());

        if ($publisher === null) {
            return $this->publisherService->create(
                $comicVineVolume->getPublisher()->getId(),
                $comicVineVolume->getPublisher()->getName()
            );
        }

        return $publisher;
    }

    public function persist(Issue\Dto $comicVineIssue, Volume\Dto $comicVineVolume) : Component\Comic\Entity
    {
        $publisher = $this->getPublisher($comicVineVolume);
        $cover = $this->getCover($comicVineIssue);

        $name = $comicVineVolume->getName();
        if ($comicVineIssue->getName() !== '') {
            $name .= ' - ' . $comicVineIssue->getName();
        }

        return $this->comicService->create(
            $comicVineIssue->getId(),
            $cover->getId(),
            PlainText::createFromString($this->convertString($name)),
            $this->getYear($comicVineIssue),
            $publisher->getId(),
            PlainText::createFromString($this->convertString($comicVineIssue->getDescription())),
            null,
            null,
            null
        );
    }

    private function convertString(string $string) : string
    {
        return strip_tags(str_replace('’', '\'', $string));
    }

    private function getYear(Issue\Dto $comicVineIssue) : ?Year
    {
        $storeDate = $comicVineIssue->getStoreDate();

        return $storeDate === null ? null : Year::createFromInt((int)substr($storeDate, 0, 4));
    }
}