<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Publisher;
use App\ValueObject\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Publishers extends AbstractController
{
    private Publisher\Service $publisherService;

    public function __construct(Publisher\Service $publisherService)
    {
        $this->publisherService = $publisherService;
    }

    public function getAll() : Response
    {
        return $this->json($this->publisherService->fetchAll());
    }

    public function getById(int $id) : Response
    {
        $publisherId = Id::createFromInt($id);

        try {
            $publisher = $this->publisherService->fetchById($publisherId);
        } catch (\RuntimeException $exception) {
            throw $this->createNotFoundException($exception->getMessage());
        }

        return $this->json($publisher);
    }
}