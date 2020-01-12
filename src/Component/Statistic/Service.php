<?php declare(strict_types=1);

namespace App\Component\Statistic;

use App\Component\Comic;
use App\Component\Publisher;

class Service
{
    private Comic\Service $comicService;

    private Publisher\Service $publisherService;

    public function __construct(Comic\Service $comicService, Publisher\Service $publisherService)
    {
        $this->comicService = $comicService;
        $this->publisherService = $publisherService;
    }

    public function getStatistics() : Dto
    {
        return Dto::create(
            $this->comicService->fetchTotalPrice(),
            $this->comicService->fetchAveragePrice(),
            $this->publisherService->fetchPublishersComicCount(),
            $this->publisherService->fetchPublishersComicCost(),
        );
    }
}