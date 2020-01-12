<?php declare(strict_types=1);

namespace App\Controller\Web;

use App\Component\Comic;
use App\Component\Comic\ValueObject\Search;
use App\Component\Image;
use App\Component\Publisher;
use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Query\SortOrder;
use App\ValueObject\Rating;
use App\ValueObject\Year;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Collection extends AbstractController
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

    public function addComic() : Response
    {
        return $this->render('collection/add-comic/index.html.twig');
    }

    public function deleteComic(int $id) : Response
    {
        $this->comicService->delete(Id::createFromInt($id));

        return $this->redirect('/collection/overview/');
    }

    public function edit(Request $request) : Response
    {
        $comicId = Id::createFromString((string)$request->get('id'));
        $comicVineId = (string)$request->get('comicVineId');
        $price = (string)$request->get('price');
        $year = (int)$request->get('year');
        $publisherName = (string)$request->get('publisherName');
        $coverImageFile = $request->files->get('coverImage');
        $addedToCollection = (string)$request->get('addedToCollection');
        $rating = (int)$request->get('rating');

        if ($coverImageFile instanceof UploadedFile) {
            $coverImageEntity = $this->imageService->createFromUploadedFile($coverImageFile);
            $this->comicService->updateCover($comicId, $coverImageEntity->getId());
        }

        $publisher = empty($publisherName) === true ? null : $this->publisherService->fetchByNameOrCreate($publisherName);

        $this->comicService->updateWithoutCover(
            $comicId,
            empty($comicVineId) === true ? null : Id::createFromString($comicVineId),
            PlainText::createFromString((string)$request->get('name')),
            empty($year) === true ? null : Year::createFromInt($year),
            PlainText::createFromString((string)$request->get('description')),
            empty($addedToCollection) === true ? null : DateTime::createFromString($addedToCollection),
            $publisher === null ? null : $publisher->getId(),
            empty($price) === true ? null : Price::createFromString($price),
            $rating === 0 ? null : Rating::createFromInt($rating)
        );

        return $this->redirect('/collection/overview/' . $comicId);
    }

    public function overview(Request $request) : Response
    {
        $search = Search::create(
            empty($request->get('term')) === true ? null : (string)$request->get('term'),
            empty($request->get('page')) === true ? null : (int)$request->get('page'),
            empty($request->get('per_page')) === true ? null : (int)$request->get('per_page'),
            empty($request->get('sortBy')) === true ? null : (string)$request->get('sortBy'),
            empty($request->get('sortOrder')) === true ? null : SortOrder::create((string)$request->get('sortOrder'))
        );

        $dtoList = Comic\DtoList::create();
        /** @var Comic\Entity $comic */
        foreach ($this->comicService->fetchBySearchTerm($search) as $comic) {
            $publisherId = $comic->getPublisherId();
            $coverId = $comic->getCoverId();

            $dtoList->add(
                Comic\Dto::createFromParameters(
                    $comic->getId(),
                    $coverId === null ? null : $this->imageService->fetchById($coverId),
                    $comic->getComicVineId(),
                    $comic->getName(),
                    $comic->getYear(),
                    $publisherId === null ? null : $this->publisherService->fetchById($publisherId),
                    $comic->getDescription(),
                    $comic->getAddedToCollection(),
                    $comic->getPrice(),
                    $comic->getRating()
                )
            );
        }

        return $this->render(
            'collection/overview/index.html.twig', [
                'comics' => $dtoList,
                'perPage' => $search->getPerPage(),
                'page' => $search->getPage(),
                'totalItems' => $this->comicService->count($search->getTerm()),
                'searchTerm' => $search->getTerm(),
                'sortBy' => $search->getSortBy(),
                'sortOrder' => $search->getSortOrder()
            ]
        );
    }

    public function postComic(Request $request) : Response
    {
        $comicVineId = (string)$request->get('comicVineId');
        $price = (string)$request->get('price');
        $year = (int)$request->get('year');
        $publisherName = (string)$request->get('publisherName');
        $coverImageFile = $request->files->get('coverImage');
        $addedToCollection = (string)$request->get('addedToCollection');
        $rating = (int)$request->get('rating');

        $publisher = empty($publisherName) === true ? null : $this->publisherService->fetchByNameOrCreate($publisherName);

        $coverImageEntity = null;
        if ($coverImageFile instanceof UploadedFile) {
            $coverImageEntity = $this->imageService->createFromUploadedFile($coverImageFile);
        }

        $comic = $this->comicService->create(
            empty($comicVineId) === true ? null : Id::createFromString($comicVineId),
            $coverImageEntity === null ? null : $coverImageEntity->getId(),
            PlainText::createFromString((string)$request->get('name')),
            empty($year) === true ? null : Year::createFromInt($year),
            $publisher === null ? null : $publisher->getId(),
            PlainText::createFromString((string)$request->get('description')),
            empty($addedToCollection) === true ? null : DateTime::createFromString($addedToCollection),
            empty($price) === true ? null : Price::createFromString($price),
            $rating === 0 ? null : Rating::createFromInt($rating)
        );

        return $this->redirect('/collection/overview/' . $comic->getId());
    }

    public function show(int $id) : Response
    {
        $comic = $this->comicService->fetchById(Id::createFromInt($id));
        $coverId = $comic->getCoverId();
        $publisherId = $comic->getPublisherId();

        $dto = Comic\Dto::createFromParameters(
            $comic->getId(),
            $coverId === null ? null : $this->imageService->fetchById($coverId),
            $comic->getComicVineId(),
            $comic->getName(),
            $comic->getYear(),
            $publisherId === null ? null : $this->publisherService->fetchById($publisherId),
            $comic->getDescription(),
            $comic->getAddedToCollection(),
            $comic->getPrice(),
            $comic->getRating()
        );

        return $this->render(
            'collection/overview/show.html.twig', [
                'comic' => $dto
            ]
        );
    }
}