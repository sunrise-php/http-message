<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AccessControlAllowOriginHeader;
use Sunrise\Http\Message\Uri;

class AccessControlAllowOriginHeaderTest extends TestCase
{
    public function testContracts()
    {
        $uri = new Uri('http://localhost');
        $header = new AccessControlAllowOriginHeader($uri);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $uri = new Uri('http://localhost');
        $header = new AccessControlAllowOriginHeader($uri);

        $this->assertSame('Access-Control-Allow-Origin', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $uri = new Uri('http://localhost');
        $header = new AccessControlAllowOriginHeader($uri);

        $this->assertSame('http://localhost', $header->getFieldValue());
    }

    public function testFieldValueWithoutUri()
    {
        $header = new AccessControlAllowOriginHeader(null);

        $this->assertSame('*', $header->getFieldValue());
    }

    public function testIgnoringUnnecessaryUriComponents()
    {
        $uri = new Uri('http://user:pass@localhost:3000/index.php?q#h');
        $header = new AccessControlAllowOriginHeader($uri);

        $this->assertSame('http://localhost:3000', $header->getFieldValue());
    }

    public function testUriWithPort()
    {
        $uri = new Uri('http://localhost:3000');
        $header = new AccessControlAllowOriginHeader($uri);

        $this->assertSame('http://localhost:3000', $header->getFieldValue());
    }

    public function testUriWithoutScheme()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The URI "//localhost" for the header "Access-Control-Allow-Origin" is not valid'
        );

        new AccessControlAllowOriginHeader(new Uri('//localhost'));
    }

    public function testUriWithoutHost()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The URI "http:" for the header "Access-Control-Allow-Origin" is not valid'
        );

        new AccessControlAllowOriginHeader(new Uri('http:'));
    }

    public function testBuild()
    {
        $uri = new Uri('http://localhost');
        $header = new AccessControlAllowOriginHeader($uri);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $uri = new Uri('http://localhost');
        $header = new AccessControlAllowOriginHeader($uri);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
