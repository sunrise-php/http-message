<?php

namespace Sunrise\Http\Message\Tests;

use Fig\Http\Message\RequestMethodInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Http\Message\Request;
use Sunrise\Uri\Uri;

class RequestTest extends TestCase
{
	public function testConstructor()
	{
		$mess = new Request();

		$this->assertInstanceOf(Message::class, $mess);
		$this->assertInstanceOf(RequestInterface::class, $mess);
	}

	// METHOD

	public function testMainLogicForMethod()
	{
		$mess = new Request();
		$copy = $mess->withMethod('POST');

		$this->assertInstanceOf(Message::class, $copy);
		$this->assertInstanceOf(RequestInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);

		// default value
		$this->assertEquals('GET', $mess->getMethod());
		// assigned value
		$this->assertEquals('POST', $copy->getMethod());
	}

	public function testLowercasedMethod()
	{
		$mess = (new Request)->withMethod('post');

		$this->assertEquals('POST', $mess->getMethod());
	}

	/**
	 * @dataProvider invalidMethodProvider
	 */
	public function testInvalidMethod($method)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Request)->withMethod($method);
	}

	/**
	 * @dataProvider figMethodsProvider
	 */
	public function testMethodsFromFig($method)
	{
		$mess = (new Request)->withMethod($method);

		$this->assertEquals($method, $mess->getMethod());
	}

	// REQUEST TARGET

	public function testMainLogicForRequestTarget()
	{
		$mess = new Request();
		$copy = $mess->withRequestTarget('/path?query');

		$this->assertInstanceOf(Message::class, $copy);
		$this->assertInstanceOf(RequestInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);

		// default value
		$this->assertEquals('/', $mess->getRequestTarget());
		// assigned value
		$this->assertEquals('/path?query', $copy->getRequestTarget());
	}

	/**
	 * @dataProvider invalidRequestTargetProvider
	 */
	public function testInvalidRequestTarget($requestTarget)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Request)->withRequestTarget($requestTarget);
	}

	public function testRequestTargetFromUriWithoutPath()
	{
		$mess = (new Request)
		->withUri(new Uri('http://localhost'));

		// returns "/" as default path
		$this->assertEquals('/', $mess->getRequestTarget());
	}

	public function testRequestTargetFromUriWithNotAbsolutePath()
	{
		$mess = (new Request)
		->withUri(new Uri('not/absolute/path?query'));

		// returns "/" as default path
		$this->assertEquals('/', $mess->getRequestTarget());
	}

	public function testRequestTargetFromUriWithAbsolutePath()
	{
		$mess = (new Request)
		->withUri(new Uri('/path'));

		$this->assertEquals('/path', $mess->getRequestTarget());
	}

	public function testRequestTargetFromUriWithAbsolutePathAndQuery()
	{
		$mess = (new Request)
		->withUri(new Uri('/path?query'));

		$this->assertEquals('/path?query', $mess->getRequestTarget());
	}

	public function testRequestTargetWithIgnoringNewUri()
	{
		$mess = (new Request)
		->withRequestTarget('/primary')
		->withUri(new Uri('/new'));

		$this->assertEquals('/primary', $mess->getRequestTarget());
	}

	/**
	 * @dataProvider variedUriFormsProvider
	 */
	public function testRequestTargetWithVariedUriForms($requestTarget)
	{
		$mess = (new Request)->withRequestTarget($requestTarget);

		$this->assertEquals($requestTarget, $mess->getRequestTarget());
	}

	// URI

	public function testMainLogicForUri()
	{
		$uri = new Uri('/');

		$mess = new Request();
		$copy = $mess->withUri($uri);

		$this->assertInstanceOf(Message::class, $copy);
		$this->assertInstanceOf(RequestInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);

		// default value
		$this->assertEquals(null, $mess->getUri());
		// assigned value
		$this->assertEquals($uri, $copy->getUri());
	}

	public function testUriWithAssigningHostHeaderFromUriHost()
	{
		$uri = new Uri('http://localhost');
		$mess = (new Request)->withUri($uri);

		$this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
	}

	public function testUriWithAssigningHostHeaderFromUriHostAndPort()
	{
		$uri = new Uri('http://localhost:3000');
		$mess = (new Request)->withUri($uri);

		$this->assertEquals($uri->getHost() . ':' . $uri->getPort(), $mess->getHeaderLine('host'));
	}

	public function testUriWithReplacingHostHeaderFromUri()
	{
		$uri = new Uri('http://localhost');

		$mess = (new Request)
		->withHeader('host', 'example.com')
		->withUri($uri);

		$this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
	}

	public function testUriWithPreservingHostHeader()
	{
		$uri = new Uri('http://localhost');

		$mess = (new Request)
		->withHeader('host', 'example.com')
		->withUri($uri, true);

		$this->assertEquals('example.com', $mess->getHeaderLine('host'));
	}

	public function testUriWithPreservingHostHeaderIfItIsEmpty()
	{
		$uri = new Uri('http://localhost');
		$mess = (new Request)->withUri($uri, true);

		$this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
	}

	// PROVIDERS

	public function invalidMethodProvider()
	{
		return [
			[''],
			["BAR\0BAZ"],
			["BAR\tBAZ"],
			["BAR\nBAZ"],
			["BAR\rBAZ"],
			["BAR BAZ"],
			["BAR,BAZ"],

			// other types
			[null],
			[false],
			[1],
			[[]],
			[new \stdClass],
		];
	}

	public function figMethodsProvider()
	{
		return [
			[RequestMethodInterface::METHOD_HEAD],
			[RequestMethodInterface::METHOD_GET],
			[RequestMethodInterface::METHOD_POST],
			[RequestMethodInterface::METHOD_PUT],
			[RequestMethodInterface::METHOD_PATCH],
			[RequestMethodInterface::METHOD_DELETE],
			[RequestMethodInterface::METHOD_PURGE],
			[RequestMethodInterface::METHOD_OPTIONS],
			[RequestMethodInterface::METHOD_TRACE],
			[RequestMethodInterface::METHOD_CONNECT],
		];
	}

	public function invalidRequestTargetProvider()
	{
		return [
			[''],
			["/path\0/"],
			["/path\t/"],
			["/path\n/"],
			["/path\r/"],
			["/path /"],

			// other types
			[null],
			[false],
			[1],
			[[]],
			[new \stdClass],
		];
	}

	public function variedUriFormsProvider()
	{
		return [
			['/path?query'],
			['http://localhost/path?query'],
			['localhost:3000'],
			['*'],
		];
	}
}
