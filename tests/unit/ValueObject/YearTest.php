<?php declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Util\Json;
use App\ValueObject\Year;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Year
 * @uses   \App\Util\Json
 */
class YearTest extends TestCase
{
    private Year $year;

    public function setUp() : void
    {
        $this->year = Year::createFromInt(2001);
    }

    public function testAsInt() : void
    {
        $this->assertSame(2001, $this->year->asInt());
    }

    public function testEnsureValidRangeThrowsException() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Year has to be between 1901 and 2155, invalid value: 0');

        Year::createFromInt(0);
    }

    public function testJsonSerialize() : void
    {
        $this->assertSame('2001', Json::encode($this->year));
    }

    public function testTooString() : void
    {
        $this->assertSame('2001', (string)$this->year);
    }
}