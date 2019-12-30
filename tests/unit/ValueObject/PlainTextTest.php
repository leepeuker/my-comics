<?php declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Util\Json;
use App\ValueObject\PlainText;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\PlainText
 * @uses   \App\Util\Json
 */
class PlainTextTest extends TestCase
{
    private PlainText $plainText;

    public function setUp() : void
    {
        $this->plainText = PlainText::createFromString('foobar');
    }

    public function testCreateFromString() : void
    {
        $this->assertEquals($this->plainText, PlainText::createFromString('foobar'));
    }

    public function testCreateFromStringThrowsInvalidArgumentExceptionException() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value contains html: <p>foobar</p>');

        PlainText::createFromString('<p>foobar</p>');
    }

    public function testJsonSerialize() : void
    {
        $this->assertSame('"foobar"', Json::encode($this->plainText));
    }

    public function testTooString() : void
    {
        $this->assertSame('foobar', (string)$this->plainText);
    }
}