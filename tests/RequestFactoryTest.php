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
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($uri, $request->getUri());

        // default body of the request...
        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
        $this->assertTrue($request->getBody()->isSeekable());
        $this->assertTrue($request->getBody()->isWritable());
        $this->assertTrue($request->getBody()->isReadable());
        $this->assertEquals('php://temp', $request->getBody()->getMetadata('uri'));
    }

    /**
     * @return void
     */
    public function testCreateRequestWithUriAsString() : void
    {
        $uri = 'http://user:password@localhost:3000/path?query#fragment';
        $request = (new RequestFactory)->createRequest('GET', $uri);

        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertEquals($uri, (string) $request->getUri());
    }
}
