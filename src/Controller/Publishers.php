<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Publisher\Repository;
use App\ValueObject\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Publishers extends AbstractController
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll() : Response
    {
        $issue = $this->repository->fetchAll();

        return $this->json($issue);
    }

    public function getById(int $id) : Response
    {
        $issue = $this->repository->fetchById(Id::createFromInt($id));

        return $this->json($issue);
    }
}