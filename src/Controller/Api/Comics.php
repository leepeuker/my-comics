<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Comic;
use App\ValueObject\Id;
use App\ValueObject\PaginatedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Comics extends AbstractController
{
    private Comic\Service $comicService;

    public function __construct(Comic\Service $comicService)
    {
        $this->comicService = $comicService;
    }

    public function getAll(Request $request) : Response
    {
        $perPage = empty($request->get('per_page')) === true ? 15 : (int)$request->get('per_page');
        $page = empty($request->get('page')) === true ? 1 : (int)$request->get('page');
        $searchTerm = empty($request->get('term')) === true ? null : (string)$request->get('term');

        if ($searchTerm !== null) {
            $items = $this->comicService->fetchBySearchTerm($searchTerm, $perPage, $page);
        } else {
            $items = $this->comicService->fetchAll($perPage, $page);
        }

        return $this->json(
            PaginatedResponse::createFromParameters(
                $items,
                $this->comicService->count(),
                $perPage,
                $page
            )
        );
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