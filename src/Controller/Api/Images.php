<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Image;
use App\ValueObject\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Images extends AbstractController
{
    private Image\Service $imageService;

    public function __construct(Image\Service $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getById(int $id) : Response
    {
        $imageId = Id::createFromInt($id);

        try {
            $image = $this->imageService->fetchById($imageId);
        } catch (\RuntimeException $exception) {
            throw $this->createNotFoundException($exception->getMessage());
        }

        return $this->json($image);
    }
}