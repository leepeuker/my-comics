<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Comic;
use App\ValueObject\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Comics extends AbstractController
{
    private Comic\Service $comicService;

    public function __construct(Comic\Service $comicService)
    {
        $this->comicService = $comicService;
    }

    public function getById(int $id) : Response
    {
        $comicId = Id::createFromInt($id);

        try {
            $comic = $this->comicService->fetchById($comicId);
        } catch (\RuntimeException $exception) {
            throw $this->createNotFoundException($exception->getMessage());
        }

        return $this->json($comic);
    }
}