<?php declare(strict_types=1);

namespace App\ValueObject;

class PlainText
{
    private string $plainText;

    private function __construct(string $plainText)
    {
        self::ensureContainsNoHtml($plainText);
        $this->plainText = trim($plainText);
    }

    public static function createFromString(string $plainText) : self
    {
        return new self($plainText);
    }

    private static function ensureContainsNoHtml(string $value) : void
    {
        if ($value !== strip_tags($value)) {
            throw new \InvalidArgumentException('Value contains html: ' . $value);
        }
    }

    public function __toString() : string
    {
        return $this->plainText;
    }

    public function jsonSerialize() : string
    {
        return $this->plainText;
    }
}