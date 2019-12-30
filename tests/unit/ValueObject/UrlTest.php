<?php declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Util\Json;
use App\ValueObject\Url;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Url
 * @uses   \App\Util\Json
 */
class UrlTest extends TestCase
{
    private Url $url;

    public function setUp() : void
    {
        $this->url = Url::createFromString('http://localhost/foo/bar');
    }

    public function testCreateFromString() : void
    {
        $this->assertEquals($this->url, Url::createFromString('http://localhost/foo/bar'));
    }

    public function testCreateFromStringThrowsInvalidArgumentExceptionException() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('String is not valid url: foobar');

        Url::createFromString('foobar');
    }

    public function testJsonSerialize() : void
    {
        $this->assertSame('"http:\/\/localhost\/foo\/bar"', Json::encode($this->url));
    }

    public function testTooString() : void
    {
        $this->assertSame('http://localhost/foo/bar', (string)$this->url);
    }

    public function testGetPath() : void
    {
        $this->assertSame('/foo/bar', $this->url->getPath());
    }
}