<?php declare(strict_types=1);

namespace App\Provider\ComicVine\Resource\Volume;

use App\ValueObject\Id;

class Dto
{
    private string $description;

    private Id $int;

    private string $name;

    private Publisher $publisher;

    private function __construct(Id $int, string $name, string $description, Publisher $publisher)
    {
        $this->int = $int;
        $this->name = $name;
        $this->description = $description;
        $this->publisher = $publisher;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromInt($data['id']),
            $data['name'],
            $data['description'],
            Publisher::createFromArray($data['publisher'])
        );
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getId() : Id
    {
        return $this->int;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getPublisher() : Publisher
    {
        return $this->publisher;
    }
}