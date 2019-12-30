<?php declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\ValueObject\DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\DateTime
 */
class DateTimeTest extends TestCase
{
    private DateTime $dateTime;

    public function setUp() : void
    {
        $this->dateTime = DateTime::createFromString('01.02.2012 10:01:01');
    }

    public function testCreateFromString() : void
    {
        $this->assertEquals($this->dateTime, DateTime::createFromString('01.02.2012 10:01:01'));
    }

    public function testFormat() : void
    {
        $this->assertSame('2012-02-01 10:01:01', $this->dateTime->format('Y-m-d H:i:s'));
    }

    public function testTooString() : void
    {
        $this->assertSame('2012-02-01 10:01:01', (string)$this->dateTime);
    }
}