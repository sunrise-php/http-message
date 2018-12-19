<?php

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Stream\StreamFactory;

class MessageTest extends TestCase
{
	public function testConstructor()
	{
		$mess = new Message();

		$this->assertInstanceOf(MessageInterface::class, $mess);
	}

	public function testProtocolVersion()
	{
		$mess = new Message();
		$copy = $mess->withProtocolVersion('2.0');

		$this->assertInstanceOf(MessageInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);

		// default value
		$this->assertEquals('1.1', $mess->getProtocolVersion());
		// assigned value
		$this->assertEquals('2.0', $copy->getProtocolVersion());
	}

	/**
	 * @dataProvider invalidProtocolVersionProvider
	 */
	public function testInvalidProtocolVersion($protocolVersion)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withProtocolVersion($protocolVersion);
	}

	public function testSetHeader()
	{
		$mess = new Message();
		$copy = $mess->withHeader('x-foo', 'bar');

		$this->assertInstanceOf(MessageInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);
		$this->assertEquals([], $mess->getHeaders());
		$this->assertEquals(['x-foo' => ['bar']], $copy->getHeaders());
	}

	public function testSetHeaderWithValueArray()
	{
		$mess = (new Message)->withHeader('x-foo', ['bar', 'baz']);

		$this->assertEquals(['x-foo' => ['bar', 'baz']], $mess->getHeaders());
	}

	public function testSetSeveralHeaders()
	{
		$mess = (new Message)
		->withHeader('x-foo', ['bar', 'baz'])
		->withHeader('x-quux', ['quuux', 'quuuux']);

		$this->assertEquals([
			'x-foo' => ['bar', 'baz'],
			'x-quux' => ['quuux', 'quuuux'],
		], $mess->getHeaders());
	}

	public function testSetHeaderLowercase()
	{
		$mess = (new Message)->withHeader('X-Foo', 'bar');

		$this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
	}

	/**
	 * @dataProvider invalidHeaderNameProvider
	 */
	public function testSetInvalidHeaderName($headerName)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withHeader($headerName, 'bar');
	}

	/**
	 * @dataProvider invalidHeaderValueProvider
	 */
	public function testSetInvalidHeaderValue($headerValue)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withHeader('x-foo', $headerValue);
	}

	/**
	 * @dataProvider invalidHeaderValueProvider
	 */
	public function testSetInvalidHeaderValueInArray($headerValue)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withHeader('x-foo', ['bar', $headerValue, 'baz']);
	}

	public function testAddHeader()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');
		$copy = $mess->withAddedHeader('x-foo', 'baz');

		$this->assertInstanceOf(MessageInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);
		$this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
		$this->assertEquals(['x-foo' => ['bar', 'baz']], $copy->getHeaders());
	}

	public function testAddHeaderWithValueArray()
	{
		$mess = (new Message)
		->withHeader('x-foo', ['bar', 'baz'])
		->withAddedHeader('x-foo', ['quux', 'quuux']);

		$this->assertEquals(['x-foo' => ['bar', 'baz', 'quux', 'quuux']], $mess->getHeaders());
	}

	public function testAddSeveralHeaders()
	{
		$mess = (new Message)
		->withHeader('x-foo', 'bar')
		->withHeader('x-baz', 'quux')
		->withAddedHeader('x-foo', 'quuux')
		->withAddedHeader('x-baz', 'quuuux');

		$this->assertEquals([
			'x-foo' => ['bar', 'quuux'],
			'x-baz' => ['quux', 'quuuux'],
		], $mess->getHeaders());
	}

	public function testAddHeaderLowercase()
	{
		$mess = (new Message)
		->withHeader('x-foo', 'bar')
		->withAddedHeader('X-Foo', 'baz');

		$this->assertEquals(['x-foo' => ['bar', 'baz']], $mess->getHeaders());
	}

	/**
	 * @dataProvider invalidHeaderNameProvider
	 */
	public function testAddInvalidHeaderName($headerName)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withAddedHeader($headerName, 'bar');
	}

	/**
	 * @dataProvider invalidHeaderValueProvider
	 */
	public function testAddInvalidHeaderValue($headerValue)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withAddedHeader('x-foo', $headerValue);
	}

	/**
	 * @dataProvider invalidHeaderValueProvider
	 */
	public function testAddInvalidHeaderValueInArray($headerValue)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Message)->withAddedHeader('x-foo', ['bar', $headerValue, 'baz']);
	}

	public function testDeleteHeader()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');
		$copy = $mess->withoutHeader('x-foo');

		$this->assertInstanceOf(MessageInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);
		$this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
		$this->assertEquals([], $copy->getHeaders());
	}

	public function testDeleteHeaderCaseInsensitive()
	{
		$mess = (new Message)
		->withHeader('x-foo', 'bar')
		->withoutHeader('X-Foo');

		$this->assertEquals([], $mess->getHeaders());
	}

	public function testReplaceHeader()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');
		$copy = $mess->withHeader('x-foo', 'baz');

		$this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
		$this->assertEquals(['x-foo' => ['baz']], $copy->getHeaders());
	}

	public function testReplaceHeaderCaseInsensitive()
	{
		$mess = (new Message)
		->withHeader('x-foo', 'bar')
		->withHeader('X-Foo', 'baz');

		$this->assertEquals(['x-foo' => ['baz']], $mess->getHeaders());
	}

	public function testHasHeader()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');

		$this->assertTrue($mess->hasHeader('x-foo'));
		$this->assertFalse($mess->hasHeader('x-bar'));
	}

	public function testHasHeaderCaseInsensitive()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');

		$this->assertTrue($mess->hasHeader('x-foo'));
		$this->assertTrue($mess->hasHeader('X-Foo'));
		$this->assertTrue($mess->hasHeader('X-FOO'));
	}

	public function testGetHeader()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');

		$this->assertEquals(['bar'], $mess->getHeader('x-foo'));
		$this->assertEquals([], $mess->getHeader('x-bar'));
	}

	public function testGetHeaderCaseInsensitive()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');

		$this->assertEquals(['bar'], $mess->getHeader('x-foo'));
		$this->assertEquals(['bar'], $mess->getHeader('X-Foo'));
		$this->assertEquals(['bar'], $mess->getHeader('X-FOO'));
	}

	public function testGetHeaderWithMultipleValue()
	{
		$mess = (new Message)->withHeader('x-foo', ['bar', 'baz', 'quux']);

		$this->assertEquals(['bar', 'baz', 'quux'], $mess->getHeader('x-foo'));
	}

	public function testGetHeaderLine()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');

		$this->assertEquals('bar', $mess->getHeaderLine('x-foo'));
		$this->assertEquals('', $mess->getHeaderLine('x-bar'));
	}

	public function testGetHeaderLineCaseInsensitive()
	{
		$mess = (new Message)->withHeader('x-foo', 'bar');

		$this->assertEquals('bar', $mess->getHeaderLine('x-foo'));
		$this->assertEquals('bar', $mess->getHeaderLine('X-Foo'));
		$this->assertEquals('bar', $mess->getHeaderLine('X-FOO'));
	}

	public function testGetHeaderLineWithMultipleValue()
	{
		$mess = (new Message)->withHeader('x-foo', ['bar', 'baz', 'quux']);

		$this->assertEquals('bar, baz, quux', $mess->getHeaderLine('x-foo'));
	}

	public function testBody()
	{
		$body =(new StreamFactory)->createStreamFromResource(\STDOUT);
		$mess = new Message();
		$copy = $mess->withBody($body);

		$this->assertInstanceOf(MessageInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);

		// default value
		$this->assertEquals(null, $mess->getBody());
		// assigned value
		$this->assertEquals($body, $copy->getBody());
	}

	// Providers...

	public function invalidProtocolVersionProvider()
	{
		return [
			[''],
			['.'],
			['1.'],
			['.1'],
			['1.1.'],
			['.1.1'],
			['1.1.1'],
			['a'],
			['a.'],
			['.a'],
			['a.a'],
			['HTTP/1.1'],

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

	public function invalidHeaderNameProvider()
	{
		return [
			[''],
			['x foo'],
			['x-foo:'],
			["x\0foo"],
			["x\tfoo"],
			["x\rfoo"],
			["x\nfoo"],

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

	public function invalidHeaderValueProvider()
	{
		return [
			["field \0 value"],
			["field \r value"],
			["field \n value"],
			[["field \0 value"]],
			[["field \r value"]],
			[["field \n value"]],

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

			[[true]],
			[[false]],
			[[1]],
			[[1.1]],
			[[[]]],
			[[new \stdClass]],
			[[\STDOUT]],
			[[null]],
			[[function(){}]],
		];
	}
}
