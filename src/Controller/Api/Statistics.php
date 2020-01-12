<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Statistic;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Statistics extends AbstractController
{
    private Statistic\Service $staticService;

    public function __construct(Statistic\Service $staticService)
    {
        $this->staticService = $staticService;
    }

    public function getAll() : Response
    {
        return $this->json($this->staticService->getStatistics());
    }
}