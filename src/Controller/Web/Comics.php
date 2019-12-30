<?php declare(strict_types=1);

namespace App\Controller\Web;

use App\Component\Comic;
use App\Component\Image;
use App\Component\Publisher;
use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Year;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Comics extends AbstractController
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

    public function edit(Request $request) : Response
    {
        $comicId = Id::createFromString($request->get('id'));
        $comicVineId = $request->get('comicVineId');
        $price = $request->get('price');
        $year = $request->get('year');
        $publisherName = $request->get('publisherName');
        $coverImageFile = $request->files->get('coverImage');

        if ($coverImageFile instanceof UploadedFile) {
            $coverImageEntity = $this->imageService->createFromUploadedFile($coverImageFile);
            $this->comicService->updateCover($comicId, $coverImageEntity->getId());
        }

        $publisher = empty($publisherName) === true ? null : $this->publisherService->fetchByNameOrCreate($request->get('publisherName'));

        $this->comicService->updateWithoutCover(
            $comicId,
            empty($comicVineId) === true ? null : Id::createFromString($comicVineId),
            PlainText::createFromString($request->get('name')),
            empty($year) === true ? null : Year::createFromInt((int)$year),
            PlainText::createFromString($request->get('description')),
            empty($request->get('addedToCollection')) === true ? null : DateTime::createFromString($request->get('addedToCollection')),
            $publisher === null ? null : $publisher->getId(),
            empty($price) === true ? null : Price::createFromString($request->get('price'))
        );

        return $this->redirect('/comics/' . $comicId);
    }

    public function list() : Response
    {
        $dtoList = Comic\DtoList::create();
        $comics = $this->comicService->fetchAll();

        /** @var Comic\Entity $comic */
        foreach ($comics as $comic) {
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
                    $comic->getPrice()
                )
            );
        }

        return $this->render(
            'comics/list.html.twig', [
                'comics' => $dtoList
            ]
        );
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
            $comic->getPrice()
        );

        return $this->render(
            'comics/show.html.twig', [
                'comic' => $dto
            ]
        );
    }
}