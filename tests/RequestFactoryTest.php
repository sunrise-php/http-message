<?php

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\RequestFactory;
use Sunrise\Uri\UriFactory;

class RequestFactoryTest extends TestCase
{
	public function testConstructor()
	{
		$factory = new RequestFactory();

		$this->assertInstanceOf(RequestFactoryInterface::class, $factory);
	}

	public function testCreateRequest()
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

	public function testCreateRequestWithUriAsString()
	{
		$uri = 'http://user:password@localhost:3000/path?query#fragment';
		$request = (new RequestFactory)->createRequest('GET', $uri);

		$this->assertInstanceOf(UriInterface::class, $request->getUri());
		$this->assertEquals($uri, (string) $request->getUri());
	}
}
