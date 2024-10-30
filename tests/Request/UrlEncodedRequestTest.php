<?php

declare(strict_types=1);

namespace Request;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Request\UrlEncodedRequest;

class UrlEncodedRequestTest extends TestCase
{
    public function testConstructorWithArray(): void
    {
        $request = new UrlEncodedRequest('POST', '/', ['foo' => 'bar']);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/', (string) $request->getUri());
        $this->assertSame('application/x-www-form-urlencoded; charset=utf-8', $request->getHeaderLine('Content-Type'));
        $this->assertStringStartsWith('php://temp', $request->getBody()->getMetadata('uri'));
        $this->assertTrue($request->getBody()->isReadable());
        $this->assertTrue($request->getBody()->isWritable());
        $this->assertSame('foo=bar', $request->getBody()->__toString());
    }

    public function testConstructorWithObject(): void
    {
        $request = new UrlEncodedRequest('POST', '/', (object) ['foo' => 'bar']);

        $this->assertSame('foo=bar', $request->getBody()->__toString());
    }

    public function testConstructorWithDefaultEncodingType(): void
    {
        $request = new UrlEncodedRequest('POST', '/', ['foo' => 'bar baz']);

        $this->assertSame('foo=bar+baz', $request->getBody()->__toString());
    }

    public function testConstructorWithEncodingType(): void
    {
        $request = new UrlEncodedRequest('POST', '/', ['foo' => 'bar baz'], UrlEncodedRequest::ENCODING_TYPE_RFC3986);

        $this->assertSame('foo=bar%20baz', $request->getBody()->__toString());
    }

    public function testConstructorWithStream(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $request = new UrlEncodedRequest('POST', '/', $body);

        $this->assertSame($body, $request->getBody());
    }
}
