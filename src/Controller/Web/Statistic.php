<?php declare(strict_types=1);

namespace App\Controller\Web;

use App\Component\Comic;
use App\Component\Image;
use App\Component\Publisher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Statistic extends AbstractController
{
    private Comic\Service $comicService;

    private Image\Service $imageService;

    private Publisher\Service $publisherService;

    public function __construct(Comic\Service $comicService, Image\Service $imageService, Publisher\Service $publisherService)
    {
        $this->comicService = $comicService;
        $this->imageService = $imageService;
        $this->publisherService = $publisherService;
    }

    public function getComicStatistics() : Response
    {
        $totalPrice = $this->comicService->fetchTotalPrice();
        $averagePrice = $this->comicService->fetchAveragePrice();

        return $this->render(
            'statistic/comics.html.twig', [
                'totalPrice' => $totalPrice,
                'averagePrice' => $averagePrice
            ]
        );
    }

    public function getPublisherStatistics() : Response
    {
        $totalPrice = $this->comicService->fetchTotalPrice();
        $averagePrice = $this->comicService->fetchAveragePrice();

        return $this->render(
            'statistic/publishers.html.twig', [
                'totalPrice' => $totalPrice,
                'averagePrice' => $averagePrice
            ]
        );
    }
}