<?php declare(strict_types=1);

namespace App\ValueObject;

class Url implements \JsonSerializable
{
    private string $url;

    private function __construct(string $url)
    {
        $this->url = $url;
    }

    public static function createFromString(string $url) : self
    {
        self::ensureValidUrl($url);

        return new self($url);
    }

    private static function ensureValidUrl(string $url) : void
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('String is not valid url: ' . $url);
        }
    }

    public function __toString() : string
    {
        return $this->url;
    }

    public function getPath() : ?string
    {
        return parse_url($this->url, PHP_URL_PATH);
    }

    public function jsonSerialize() : string
    {
        return $this->url;
    }
}