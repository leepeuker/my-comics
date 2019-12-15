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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Comics extends AbstractController
{
    private Comic\Repository $comicRepo;

    private Image\Repository $imageRepo;

    private Publisher\Repository $publisherRepo;

    public function __construct(Comic\Repository $comicRepo, Image\Repository $imageRepo, Publisher\Repository $publisherRepo)
    {
        $this->comicRepo = $comicRepo;
        $this->imageRepo = $imageRepo;
        $this->publisherRepo = $publisherRepo;
    }

    public function edit(Request $request, int $id) : Response
    {
        $comicVineId = $request->get('comicVineId');
        $price = $request->get('price');
        $year = $request->get('year');
        $publisherName = $request->get('publisherName');

        $publisher = empty($publisherName) === true ? null : $this->publisherRepo->fetchByNameOrCreate($request->get('publisherName'));

        $this->comicRepo->updateWithoutCover(
            Id::createFromString($request->get('id')),
            empty($comicVineId) === true ? null : Id::createFromString($comicVineId),
            PlainText::createFromString($request->get('name')),
            empty($year) === true ? null : Year::createFromInt((int)$year),
            PlainText::createFromString($request->get('description')),
            empty($request->get('addedToCollection')) === true ? null : DateTime::createFromString($request->get('addedToCollection')),
            $publisher === null ? null : $publisher->getId(),
            empty($price) === true ? null : Price::createFromString($request->get('price'))
        );

        return $this->redirect('/comics/' . $id);
    }

    public function list() : Response
    {
        $dtoList = Comic\DtoList::create();
        $comics = $this->comicRepo->fetchAll();

        /** @var Comic\Entity $comic */
        foreach ($comics as $comic) {
            $dtoList->add(
                Comic\Dto::createFromParameters(
                    $comic->getId(),
                    $comic->getCoverId() === null ? null : $this->imageRepo->fetchById($comic->getCoverId()),
                    $comic->getComicVineId(),
                    $comic->getName(),
                    $comic->getYear(),
                    $this->publisherRepo->fetchById($comic->getPublisherId()),
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
        $comic = $this->comicRepo->fetchById(Id::createFromInt($id));
        $dto = Comic\Dto::createFromParameters(
            $comic->getId(),
            $comic->getCoverId() === null ? null : $this->imageRepo->fetchById($comic->getCoverId()),
            $comic->getComicVineId(),
            $comic->getName(),
            $comic->getYear(),
            $comic->getPublisherId() === null ? null : $this->publisherRepo->fetchById($comic->getPublisherId()),
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