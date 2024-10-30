<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Response;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Response\HtmlResponse;

class HtmlResponseTest extends TestCase
{
    public function testConstructorWithStringHtml(): void
    {
        $response = new HtmlResponse(400, 'foo');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('text/html; charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertStringStartsWith('php://temp', $response->getBody()->getMetadata('uri'));
        $this->assertTrue($response->getBody()->isReadable());
        $this->assertTrue($response->getBody()->isWritable());
        $this->assertSame('foo', $response->getBody()->__toString());
    }

    public function testConstructorWithStringableHtml(): void
    {
        $response = new HtmlResponse(200, new class
        {
            public function __toString(): string
            {
                return 'foo';
            }
        });

        $this->assertSame('foo', $response->getBody()->__toString());
    }

    public function testConstructorWithUnexpectedHtml(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to create the HTML response due to a unexpected HTML type');

        new HtmlResponse(200, null);
    }

    public function testConstructorWithStream(): void
    {
        $html = $this->createMock(StreamInterface::class);
        $response = new HtmlResponse(200, $html);

        $this->assertSame($html, $response->getBody());
    }
}
