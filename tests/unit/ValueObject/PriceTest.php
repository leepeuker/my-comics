<?php declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Util\Json;
use App\ValueObject\Price;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Price
 * @uses   \App\Util\Json
 */
class PriceTest extends TestCase
{
    private Price $price;

    public function setUp() : void
    {
        $this->price = Price::createFromInt(100);
    }

    public function testAsInt() : void
    {
        $this->assertSame(100, $this->price->asInt());
    }

    public function testCreateFromString() : void
    {
        $this->assertEquals($this->price, Price::createFromString('100'));
    }

    public function testCreateFromStringThrowsInvalidArgumentExceptionException() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Price contains more than digits: foobar');

        Price::createFromString('foobar');
    }

    public function testJsonSerialize() : void
    {
        $this->assertSame('100', Json::encode($this->price));
    }

    public function testTooString() : void
    {
        $this->assertSame('100', (string)$this->price);
    }
}