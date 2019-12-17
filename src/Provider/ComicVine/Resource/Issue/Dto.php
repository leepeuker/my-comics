<?php declare(strict_types=1);

namespace App\Provider\ComicVine\Resource\Issue;

use App\ValueObject\Id;
use App\ValueObject\Url;

class Dto
{
    private Url $coverUrl;

    private string $description;

    private Id $id;

    private int $issueNumber;

    private string $name;

    private ?string $storeDate;

    private Volume $volume;

    private function __construct(Id $id, Url $coverUrl, string $name, string $description, ?string $storeDate, int $issueNumber, Volume $volume)
    {
        $this->id = $id;
        $this->coverUrl = $coverUrl;
        $this->name = $name;
        $this->description = $description;
        $this->storeDate = $storeDate;
        $this->issueNumber = $issueNumber;
        $this->volume = $volume;
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            Id::createFromInt((int)$data['id']),
            Url::createFromString((string)$data['image']['medium_url']),
            (string)$data['name'],
            (string)$data['description'],
            $data['store_date'] === null ? null : (string)$data['store_date'],
            (int)$data['issue_number'],
            Volume::createFromArray($data['volume']),
        );
    }

    public function getCoverUrl() : Url
    {
        return $this->coverUrl;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getId() : Id
    {
        return $this->id;
    }

    public function getIssueNumber() : int
    {
        return $this->issueNumber;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getStoreDate() : ?string
    {
        return $this->storeDate;
    }

    public function getVolume() : Volume
    {
        return $this->volume;
    }
}