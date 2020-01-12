<?php declare(strict_types=1);

namespace App\Component\Statistic;

class Dto implements \JsonSerializable
{
    private int $averagePrice;

    private int $totalPrice;

    private function __construct(int $totalPrice, int $averagePrice)
    {
        $this->totalPrice = $totalPrice;
        $this->averagePrice = $averagePrice;
    }

    public static function create(int $totalPrice, int $averagePrice) : self
    {
        return new self($totalPrice, $averagePrice);
    }

    public function getAveragePrice() : int
    {
        return $this->averagePrice;
    }

    public function getTotalPrice() : int
    {
        return $this->totalPrice;
    }

    public function jsonSerialize() : array
    {
        return [
            'totalPrice' => $this->totalPrice,
            'averagePrice' => $this->averagePrice,
        ];
    }
}