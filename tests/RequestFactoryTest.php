<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\RequestFactory;
use Sunrise\Uri\UriFactory;

/**
 * RequestFactoryTest
 */
class RequestFactoryTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $factory = new RequestFactory();

        $this->assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    /**
     * @return void
     */
    public function testCreateRequest() : void
    {
        $method = 'POST';
        $uri = (new UriFactory)->createUri('/');
        $request = (new RequestFactory)->createRequest($method, $uri);

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($uri, $request->getUri());

        // default body of the request...
        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
        $this->assertTrue($request->getBody()->isSeekable());
        $this->assertTrue($request->getBody()->isWritable());
        $this->assertTrue($request->getBody()->isReadable());
        $this->assertSame('php://temp', $request->getBody()->getMetadata('uri'));
    }

    /**
     * @return void
     */
    public function testCreateRequestWithUriAsString() : void
    {
        $uri = 'http://user:password@localhost:3000/path?query#fragment';
        $request = (new RequestFactory)->createRequest('GET', $uri);

        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertSame($uri, (string) $request->getUri());
    }

    /**
     * @return void
     */
    public function testCreateJsonRequest() : void
    {
        $payload = ['foo' => '<bar>'];
        $options = \JSON_HEX_TAG;

        $request = (new RequestFactory)->createJsonRequest('GET', '/foo', $payload, $options);

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
        $this->assertSame('application/json; charset=UTF-8', $request->getHeaderLine('Content-Type'));
        $this->assertSame(\json_encode($payload, $options), (string) $request->getBody());
    }

    /**
     * @return void
     */
    public function testCreateJsonRequestWithInvalidJson() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum stack depth exceeded');

        $request = (new RequestFactory)->createJsonRequest('GET', '/', [[]], 0, 1);
    }
}
