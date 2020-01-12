<?php declare(strict_types=1);

namespace App\Component\Statistic;

class Dto implements \JsonSerializable
{
    private int $averagePrice;

    private array $publishersComicCount;

    private int $totalPrice;

    private function __construct(int $totalPrice, int $averagePrice, array $publishersComicCount)
    {
        $this->totalPrice = $totalPrice;
        $this->averagePrice = $averagePrice;
        $this->publishersComicCount = $publishersComicCount;
    }

    public static function create(int $totalPrice, int $averagePrice, array $publishersComicCount) : self
    {
        return new self($totalPrice, $averagePrice, $publishersComicCount);
    }

    public function jsonSerialize() : array
    {
        return [
            'totalPrice' => $this->totalPrice,
            'averagePrice' => $this->averagePrice,
            'publishersComicCount' => $this->publishersComicCount,
        ];
    }
}