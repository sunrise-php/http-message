<?php

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\RequestFactory;
use Sunrise\Uri\Uri;

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
		$uri = new Uri('/');

		$request = (new RequestFactory)
		->createRequest($method, $uri);

		$this->assertInstanceOf(RequestInterface::class, $request);
		$this->assertEquals($method, $request->getMethod());
		$this->assertEquals($uri, $request->getUri());
	}

	public function testCreateRequestWithUriAsString()
	{
		$uri = 'http://user:password@localhost:3000/path?query#fragment';

		$request = (new RequestFactory)
		->createRequest('GET', $uri);

		$this->assertInstanceOf(UriInterface::class, $request->getUri());
		$this->assertEquals($uri, (string) $request->getUri());
	}
}
