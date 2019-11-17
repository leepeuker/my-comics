<?php declare(strict_types=1);

namespace App\Controller\Web;

use App\Component\Comic;
use App\Component\Image;
use App\Component\Publisher;
use App\ValueObject\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function list() : Response
    {
        $dtoList = Comic\DtoList::create();
        $comics = $this->comicRepo->fetchAll();

        /** @var Comic\Entity $comic */
        foreach ($comics as $comic) {
            $dtoList->add(
                Comic\Dto::createFromParameters(
                    $comic->getId(),
                    $this->imageRepo->fetchById($comic->getCoverId()),
                    $comic->getComicVineId(),
                    $comic->getName(),
                    $comic->getYear(),
                    $this->publisherRepo->fetchById($comic->getPublisherId()),
                    $comic->getDescription(),
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
            $this->imageRepo->fetchById($comic->getCoverId()),
            $comic->getComicVineId(),
            $comic->getName(),
            $comic->getYear(),
            $this->publisherRepo->fetchById($comic->getPublisherId()),
            $comic->getDescription(),
            $comic->getPrice()
        );

        return $this->render(
            'comics/show.html.twig', [
                'comic' => $dto
            ]
        );
    }
}