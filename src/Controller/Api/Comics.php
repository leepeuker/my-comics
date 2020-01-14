<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Comic;
use App\Component\Comic\ValueObject\Search;
use App\Component\Publisher;
use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\PaginatedResponse;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Rating;
use App\ValueObject\Year;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Comics extends AbstractController
{
    private Comic\Service $comicService;

    private Publisher\Service $publisherService;

    public function __construct(Comic\Service $comicService, Publisher\Service $publisherService)
    {
        $this->comicService = $comicService;
        $this->publisherService = $publisherService;
    }

    public function delete(int $id) : Response
    {
        $this->comicService->delete(Id::createFromInt($id));

        return Response::create(null, Response::HTTP_NO_CONTENT);
    }

    public function getAll(Request $request) : Response
    {
        $search = Search::createFromResponse($request);

        return $this->json(
            PaginatedResponse::createFromParameters(
                $this->comicService->fetchBySearchTerm($search),
                $this->comicService->count($search->getTerm()),
                $search->getPerPage(),
                $search->getPage()
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

    public function post(Request $request) : Response
    {
        $comicVineId = $request->request->get('comicVineId');
        $coverImageId = $request->request->get('coverImageId');
        $year = $request->request->get('year');
        $publisherName = $request->request->get('publisherName');
        $addedToCollection = $request->request->get('addedToCollection');
        $price = $request->request->get('price');
        $rating = $request->request->get('rating');

        $publisher = $publisherName === null ? null : $this->publisherService->fetchByNameOrCreate((string)$publisherName);

        $comic = $this->comicService->create(
            empty($comicVineId) === true ? null : Id::createFromString($comicVineId),
            empty($coverImageId) === true ? null : Id::createFromString($coverImageId),
            PlainText::createFromString((string)$request->request->get('name')),
            empty($year) === true ? null : Year::createFromInt((int)$year),
            $publisher === null ? null : $publisher->getId(),
            PlainText::createFromString((string)$request->request->get('description')),
            $addedToCollection === null ? null : DateTime::createFromString($addedToCollection),
            empty($price) === true ? null : Price::createFromString($price),
            empty($rating) === true ? null : Rating::createFromString($rating)
        );

        return $this->json($comic, Response::HTTP_CREATED);
    }
}