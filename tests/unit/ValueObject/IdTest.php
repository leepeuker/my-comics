<?php declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Util\Json;
use App\ValueObject\Id;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Id
 * @uses   \App\Util\Json
 */
class IdTest extends TestCase
{
    private Id $id;

    public function setUp() : void
    {
        $this->id = Id::createFromInt(100);
    }

    public function testAsInt() : void
    {
        $this->assertSame(100, $this->id->asInt());
    }

    public function testCreateFromString() : void
    {
        $this->assertEquals($this->id, Id::createFromString('100'));
    }

    public function testCreateFromStringThrowsInvalidArgumentExceptionException() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id contains more than digits: foobar');

        Id::createFromString('foobar');
    }

    public function testJsonSerialize() : void
    {
        $this->assertSame('100', Json::encode($this->id));
    }

    public function testTooString() : void
    {
        $this->assertSame('100', (string)$this->id);
    }
}