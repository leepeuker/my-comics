<?php declare(strict_types=1);

namespace App\Component\Comic;

use App\ValueObject\DateTime;
use App\ValueObject\Id;
use App\ValueObject\PlainText;
use App\ValueObject\Price;
use App\ValueObject\Rating;
use App\ValueObject\Year;

class Entity implements \JsonSerializable
{
    private ?DateTime $addedToCollection;

    private ?Id $comicVineId;

    private ?Id $coverId;

    private DateTime $createdAt;

    private PlainText $description;

    private Id $id;

    private PlainText $name;

    private ?Price $price;

    private ?Id $publisherId;

    private ?Rating $rating;

    private ?DateTime $updatedAt;

    private ?Year $year;

    private function __construct(
        Id $id,
        ?Id $coverId,
        ?Id $comicVineId,
        PlainText $name,
        ?Year $year,
        ?Id $publisherId,
        PlainText $description,
        ?DateTime $addedToCollection,
        ?Price $price,
        ?Rating $rating,
        DateTime $createdAt,
        ?DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->coverId = $coverId;
        $this->comicVineId = $comicVineId;
        $this->name = $name;
        $this->year = $year;
        $this->publisherId = $publisherId;
        $this->description = $description;
        $this->addedToCollection = $addedToCollection;
        $this->price = $price;
        $this->rating = $rating;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromString((string)$data['id']),
            empty($data['cover_id']) === true ? null : Id::createFromString((string)$data['cover_id']),
            $data['comic_vine_id'] === null ? null : Id::createFromString((string)$data['comic_vine_id']),
            PlainText::createFromString((string)$data['name']),
            empty($data['year']) === true ? null : Year::createFromInt((int)$data['year']),
            empty($data['publisher_id']) === true ? null : Id::createFromString((string)$data['publisher_id']),
            PlainText::createFromString((string)$data['description']),
            empty($data['added_to_collection']) === true ? null : DateTime::createFromString((string)$data['added_to_collection']),
            $data['price'] === null ? null : Price::createFromString((string)$data['price']),
            $data['rating'] === null ? null : Rating::createFromString((string)$data['rating']),
            DateTime::createFromString((string)$data['created_at']),
            empty($data['updated_at']) === false ? DateTime::createFromString((string)$data['updated_at']) : null
        );
    }

    public function getAddedToCollection() : ?DateTime
    {
        return $this->addedToCollection;
    }

    public function getComicVineId() : ?Id
    {
        return $this->comicVineId;
    }

    public function getCoverId() : ?Id
    {
        return $this->coverId;
    }

    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    public function getDescription() : PlainText
    {
        return $this->description;
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getName() : PlainText
    {
        return $this->name;
    }

    public function getPrice() : ?Price
    {
        return $this->price;
    }

    public function getPublisherId() : ?Id
    {
        return $this->publisherId;
    }

    public function getRating() : ?Rating
    {
        return $this->rating;
    }

    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    public function getYear() : ?Year
    {
        return $this->year;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'coverId' => $this->coverId,
            'comicVineId' => $this->comicVineId,
            'name' => $this->name,
            'year' => $this->year,
            'publisherId' => $this->publisherId,
            'description' => $this->description,
            'price' => $this->price,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}