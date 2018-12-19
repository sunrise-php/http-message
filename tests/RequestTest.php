<?php

namespace Sunrise\Http\Message\Tests;

use Fig\Http\Message\RequestMethodInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Http\Message\Request;
use Sunrise\Uri\UriFactory;

class RequestTest extends TestCase
{
	public function testConstructor()
	{
		$mess = new Request();

		$this->assertInstanceOf(Message::class, $mess);
		$this->assertInstanceOf(RequestInterface::class, $mess);
	}

	public function testMethod()
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
	 * @dataProvider figMethodProvider
	 */
	public function testFigMethod($method)
	{
		$mess = (new Request)->withMethod($method);

		$this->assertEquals($method, $mess->getMethod());
	}

	/**
	 * @dataProvider invalidMethodProvider
	 */
	public function testInvalidMethod($method)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Request)->withMethod($method);
	}

	public function testRequestTarget()
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
	 * @dataProvider uriFormProvider
	 */
	public function testRequestTargetWithDifferentUriForms($requestTarget)
	{
		$mess = (new Request)->withRequestTarget($requestTarget);

		$this->assertEquals($requestTarget, $mess->getRequestTarget());
	}

	/**
	 * @dataProvider invalidRequestTargetProvider
	 */
	public function testInvalidRequestTarget($requestTarget)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Request)->withRequestTarget($requestTarget);
	}

	public function testGetRequestTargetHavingUriWithoutPath()
	{
		$uri = (new UriFactory)->createUri('http://localhost');
		$mess = (new Request)->withUri($uri);

		// returns "/" as default path
		$this->assertEquals('/', $mess->getRequestTarget());
	}

	public function testGetRequestTargetHavingUriWithNotAbsolutePath()
	{
		$uri = (new UriFactory)->createUri('not/absolute/path?query');
		$mess = (new Request)->withUri($uri);

		// returns "/" as default path
		$this->assertEquals('/', $mess->getRequestTarget());
	}

	public function testGetRequestTargetHavingUriWithAbsolutePath()
	{
		$uri = (new UriFactory)->createUri('/path');
		$mess = (new Request)->withUri($uri);

		$this->assertEquals('/path', $mess->getRequestTarget());
	}

	public function testGetRequestTargetHavingUriWithAbsolutePathAndQuery()
	{
		$uri = (new UriFactory)->createUri('/path?query');
		$mess = (new Request)->withUri($uri);

		$this->assertEquals('/path?query', $mess->getRequestTarget());
	}

	public function testGetRequestTargetIgnoringNewUri()
	{
		$uri = (new UriFactory)->createUri('/new');
		$mess = (new Request)->withRequestTarget('/primary')->withUri($uri);

		$this->assertEquals('/primary', $mess->getRequestTarget());
	}

	public function testUri()
	{
		$uri = (new UriFactory)->createUri('/');
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
		$uri = (new UriFactory)->createUri('http://localhost');
		$mess = (new Request)->withUri($uri);

		$this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
	}

	public function testUriWithAssigningHostHeaderFromUriHostAndPort()
	{
		$uri = (new UriFactory)->createUri('http://localhost:3000');
		$mess = (new Request)->withUri($uri);

		$this->assertEquals($uri->getHost() . ':' . $uri->getPort(), $mess->getHeaderLine('host'));
	}

	public function testUriWithReplacingHostHeaderFromUri()
	{
		$uri = (new UriFactory)->createUri('http://localhost');
		$mess = (new Request)->withHeader('host', 'example.com')->withUri($uri);

		$this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
	}

	public function testUriWithPreservingHostHeader()
	{
		$uri = (new UriFactory)->createUri('http://localhost');
		$mess = (new Request)->withHeader('host', 'example.com')->withUri($uri, true);

		$this->assertEquals('example.com', $mess->getHeaderLine('host'));
	}

	public function testUriWithPreservingHostHeaderIfItIsEmpty()
	{
		$uri = (new UriFactory)->createUri('http://localhost');
		$mess = (new Request)->withUri($uri, true);

		$this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
	}

	// Providers...

	public function figMethodProvider()
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
			[true],
			[false],
			[1],
			[1.1],
			[[]],
			[new \stdClass],
			[\STDOUT],
			[null],
			[function(){}],
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
			[true],
			[false],
			[1],
			[1.1],
			[[]],
			[new \stdClass],
			[\STDOUT],
			[null],
			[function(){}],
		];
	}

	public function uriFormProvider()
	{
		return [
			// https://tools.ietf.org/html/rfc7230#section-5.3.1
			['/path?query'],

			// https://tools.ietf.org/html/rfc7230#section-5.3.2
			['http://localhost/path?query'],

			// https://tools.ietf.org/html/rfc7230#section-5.3.3
			['localhost:3000'],

			// https://tools.ietf.org/html/rfc7230#section-5.3.4
			['*'],
		];
	}
}
