<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\Component\Image;
use App\Component\Publisher;
use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\Price;
use App\ValueObject\Year;

class Dto implements \JsonSerializable
{
    private ?DateTime $addedToCollection;

    private ?Id $comicVineId;

    private ?Image\Entity $cover;

    private string $description;

    private Id $id;

    private string $name;

    private ?Price $price;

    private ?Publisher\Entity $publisher;

    private ?Year $year;

    private function __construct(
        Id $id,
        ?Image\Entity $cover,
        ?Id $comicVineId,
        string $name,
        ?Year $year,
        ?Publisher\Entity $publisher,
        string $description,
        ?DateTime $addedToCollection,
        ?Price $price
    ) {
        $this->id = $id;
        $this->cover = $cover;
        $this->comicVineId = $comicVineId;
        $this->name = $name;
        $this->year = $year;
        $this->publisher = $publisher;
        $this->description = $description;
        $this->addedToCollection = $addedToCollection;
        $this->price = $price;
    }

    public static function createFromParameters(
        Id $id,
        ?Image\Entity $cover,
        ?Id $comicVineId,
        string $name,
        ?Year $year,
        ?Publisher\Entity $publisher,
        string $description,
        ?DateTime $addedToCollection,
        ?Price $price
    ) : self {
        return new self($id, $cover, $comicVineId, $name, $year, $publisher, $description, $addedToCollection, $price);
    }

    public function getAddedToCollection() : ?DateTime
    {
        return $this->addedToCollection;
    }

    public function getComicVineId() : ?Id
    {
        return $this->comicVineId;
    }

    public function getCover() : ?Image\Entity
    {
        return $this->cover;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getPrice() : ?Price
    {
        return $this->price;
    }

    public function getPublisher() : ?Publisher\Entity
    {
        return $this->publisher;
    }

    public function getYear() : ?Year
    {
        return $this->year;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'cover' => $this->cover,
            'comicVineId' => $this->comicVineId,
            'name' => $this->name,
            'year' => $this->year,
            'publisher' => $this->publisher,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}