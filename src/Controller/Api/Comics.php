<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Comic;
use App\Component\Comic\ValueObject\Search;
use App\Component\Publisher;
use App\Provider\ComicVine\ApiNoResultFoundException;
use App\Service\ComicVine;
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

    private ComicVine $comicVineService;

    private Publisher\Service $publisherService;

    public function __construct(Comic\Service $comicService, Publisher\Service $publisherService, ComicVine $comicVineService)
    {
        $this->comicService = $comicService;
        $this->publisherService = $publisherService;
        $this->comicVineService = $comicVineService;
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
            empty($comicVineId) === true ? null : Id::createFromString((string)$comicVineId),
            empty($coverImageId) === true ? null : Id::createFromString((string)$coverImageId),
            PlainText::createFromString((string)$request->request->get('name')),
            empty($year) === true ? null : Year::createFromInt((int)$year),
            $publisher === null ? null : $publisher->getId(),
            PlainText::createFromString((string)$request->request->get('description')),
            $addedToCollection === null ? null : DateTime::createFromString((string)$addedToCollection),
            empty($price) === true ? null : Price::createFromString((string)$price),
            empty($rating) === true ? null : Rating::createFromString((string)$rating)
        );

        return $this->json($comic, Response::HTTP_CREATED);
    }

    public function postComicVineId(Request $request) : Response
    {
        $comicVineId = (string)$request->request->get('comicVineId');

        try {
            $issue = $this->comicVineService->createComicByIssueId(Id::createFromString($comicVineId));
        } catch (ApiNoResultFoundException $e) {
            return $this->json(['errorMessage' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($issue, Response::HTTP_CREATED);
    }

    public function put(Request $request, int $id) : Response
    {
        $comicId = Id::createFromInt($id);

        $comicVineId = $request->request->get('comicVineId');
        $publisherId = $request->request->get('publisherId');
        $price = $request->request->get('price');
        $coverImageId = $request->request->get('coverId');
        $year = $request->request->get('year');
        $addedToCollection = $request->request->get('addedToCollection');
        $rating = $request->request->get('rating');

        $this->comicService->update(
            $comicId,
            empty($comicVineId) === true ? null : Id::createFromString((string)$comicVineId),
            empty($coverImageId) === true ? null : Id::createFromString((string)$coverImageId),
            PlainText::createFromString((string)$request->request->get('name')),
            empty($year) === true ? null : Year::createFromInt((int)$year),
            PlainText::createFromString((string)$request->request->get('description')),
            empty($addedToCollection) === true ? null : DateTime::createFromString((string)$addedToCollection),
            empty($publisherId) === true ? null : Id::createFromString((string)$publisherId),
            empty($price) === true ? null : Price::createFromInt((int)$price),
            empty($rating) === true ? null : Rating::createFromString((string)$rating)
        );

        return $this->json($this->comicService->fetchById($comicId), Response::HTTP_OK);
    }
}