<?php declare(strict_types=1);

namespace App\Component\Statistic;

class Dto implements \JsonSerializable
{
    private int $averagePrice;

    private array $publishersComicCost;

    private array $publishersComicCount;

    private int $totalPrice;

    private function __construct(int $totalPrice, int $averagePrice, array $publishersComicCount, array $publishersComicCost)
    {
        $this->totalPrice = $totalPrice;
        $this->averagePrice = $averagePrice;
        $this->publishersComicCount = $publishersComicCount;
        $this->publishersComicCost = $publishersComicCost;
    }

    public static function create(int $totalPrice, int $averagePrice, array $publishersComicCount, array $publishersComicCos) : self
    {
        return new self($totalPrice, $averagePrice, $publishersComicCount, $publishersComicCos);
    }

    public function jsonSerialize() : array
    {
        return [
            'totalPrice' => $this->totalPrice,
            'averagePrice' => $this->averagePrice,
            'publishersComicCount' => $this->publishersComicCount,
            'publishersComicCost' => $this->publishersComicCost
        ];
    }
}