<?php declare(strict_types=1);

namespace App\Component\Statistic;

use App\Component\Comic;

class Service
{
    private Comic\Service $comicService;

    public function __construct(Comic\Service $comicService)
    {
        $this->comicService = $comicService;
    }

    public function getStatistics() : Dto
    {
        return Dto::create(
            $this->comicService->fetchTotalPrice(),
            $this->comicService->fetchAveragePrice(),
        );
    }
}