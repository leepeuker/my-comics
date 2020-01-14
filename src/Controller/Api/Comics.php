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
        $comicVineId = $request->request->has('comicVineId') === true ? $request->request->getInt('comicVineId') : null;
        $coverImageId = $request->request->has('coverImageId') === true ? $request->request->getInt('coverImageId') : null;
        $year = $request->request->has('year') === true ? $request->request->getInt('year') : null;
        $publisherName = $request->request->has('publisherName') === true ? (string)$request->request->get('publisherName') : null;
        $price = $request->request->has('price') === true ? $request->request->getInt('price') : null;
        $addedToCollection = $request->request->has('addedToCollection') === true ? (string)$request->request->get('addedToCollection') : null;
        $rating = $request->request->has('rating') === true ? $request->request->getInt('rating') : null;

        $publisher = $publisherName === null ? null : $this->publisherService->fetchByNameOrCreate($publisherName);

        $comic = $this->comicService->create(
            $comicVineId === null ? null : Id::createFromInt($comicVineId),
            $coverImageId === null ? null : Id::createFromInt($coverImageId),
            PlainText::createFromString((string)$request->request->get('name')),
            $year === null ? null : Year::createFromInt($year),
            $publisher === null ? null : $publisher->getId(),
            PlainText::createFromString((string)$request->request->get('description')),
            $addedToCollection === null ? null : DateTime::createFromString($addedToCollection),
            $price === null ? null : Price::createFromInt($price),
            $rating === null ? null : Rating::createFromInt($rating)
        );

        return $this->json($comic, Response::HTTP_CREATED);
    }
}