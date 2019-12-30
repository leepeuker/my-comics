<?php declare(strict_types=1);

namespace App\Component\Publisher;

use App\ValueObject\Id;

class Service
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function create(Id $comicVineId, string $name) : Entity
    {
        return $this->repository->create($comicVineId, $name);
    }

    public function fetchAll() : EntityList
    {
        return $this->repository->fetchAll();
    }

    public function fetchById(Id $id) : ?Entity
    {
        return $this->repository->fetchById($id);
    }

    public function fetchByName(string $name) : ?Entity
    {
        return $this->repository->fetchByName($name);
    }

    public function fetchByNameOrCreate(string $name) : Entity
    {
        $publisher = $this->repository->fetchByName($name);

        if ($publisher !== null) {
            return $publisher;
        }

        return $this->repository->create(null, $name);
    }
}