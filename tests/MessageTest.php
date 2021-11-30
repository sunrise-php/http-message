<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Stream\StreamFactory;

/**
 * MessageTest
 */
class MessageTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $mess = new Message();

        $this->assertInstanceOf(MessageInterface::class, $mess);
    }

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $headers = ['X-Foo' => ['bar', 'baz'], 'X-Bar' => ['baz']];
        $body = (new StreamFactory)->createStreamFromResource(\STDOUT);
        $protocol = '2.0';

        $mess = new Message($headers, $body, $protocol);

        $this->assertSame($headers, $mess->getHeaders());
        $this->assertSame($body, $mess->getBody());
        $this->assertSame($protocol, $mess->getProtocolVersion());
    }

    /**
     * @return void
     */
    public function testProtocolVersion() : void
    {
        $mess = new Message();
        $copy = $mess->withProtocolVersion('2.0');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);

        // default value
        $this->assertSame('1.1', $mess->getProtocolVersion());
        // assigned value
        $this->assertSame('2.0', $copy->getProtocolVersion());
    }

    /**
     * @dataProvider invalidProtocolVersionProvider
     *
     * @return void
     */
    public function testInvalidProtocolVersion($protocolVersion) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withProtocolVersion($protocolVersion);
    }

    /**
     * @return void
     */
    public function testSetHeader() : void
    {
        $mess = new Message();
        $copy = $mess->withHeader('X-Foo', 'bar');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);
        $this->assertSame([], $mess->getHeaders());
        $this->assertSame(['X-Foo' => ['bar']], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetHeaderWithSeveralValues() : void
    {
        $mess = (new Message)->withHeader('X-Foo', ['bar', 'baz']);

        $this->assertSame(['X-Foo' => ['bar', 'baz']], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetSeveralHeaders() : void
    {
        $mess = (new Message)
            ->withHeader('X-Foo', ['bar', 'baz'])
            ->withHeader('X-Quux', ['quuux', 'quuuux']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
            'X-Quux' => ['quuux', 'quuuux'],
        ], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetHeaderLowercase() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertSame(['X-Foo' => ['bar']], $mess->getHeaders());
    }

    /**
     * @dataProvider invalidHeaderNameProvider
     *
     * @return void
     */
    public function testSetInvalidHeaderName($headerName) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withHeader($headerName, 'bar');
    }

    /**
     * @dataProvider invalidHeaderValueProvider
     *
     * @return void
     */
    public function testSetInvalidHeaderValue($headerValue) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withHeader('X-Foo', $headerValue);
    }

    /**
     * @dataProvider invalidHeaderValueProvider
     *
     * @return void
     */
    public function testSetInvalidHeaderValueItem($headerValue) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withHeader('X-Foo', ['bar', $headerValue, 'baz']);
    }

    /**
     * @return void
     */
    public function testAddHeader() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');
        $copy = $mess->withAddedHeader('X-Foo', 'baz');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);
        $this->assertSame(['X-Foo' => ['bar']], $mess->getHeaders());
        $this->assertSame(['X-Foo' => ['bar', 'baz']], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testAddHeaderWithSeveralValues() : void
    {
        $mess = (new Message)
            ->withHeader('X-Foo', ['bar', 'baz'])
            ->withAddedHeader('X-Foo', ['quux', 'quuux']);

        $this->assertSame(['X-Foo' => ['bar', 'baz', 'quux', 'quuux']], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testAddSeveralHeaders() : void
    {
        $mess = (new Message)
            ->withHeader('X-Foo', 'bar')
            ->withHeader('X-Baz', 'quux')
            ->withAddedHeader('X-Foo', 'quuux')
            ->withAddedHeader('X-Baz', 'quuuux');

        $this->assertSame([
            'X-Foo' => ['bar', 'quuux'],
            'X-Baz' => ['quux', 'quuuux'],
        ], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testAddHeaderLowercase() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', 'bar')
            ->withAddedHeader('x-foo', 'baz');

        $this->assertSame(['X-Foo' => ['bar', 'baz']], $mess->getHeaders());
    }

    /**
     * @dataProvider invalidHeaderNameProvider
     *
     * @return void
     */
    public function testAddInvalidHeaderName($headerName) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withAddedHeader($headerName, 'bar');
    }

    /**
     * @dataProvider invalidHeaderValueProvider
     *
     * @return void
     */
    public function testAddInvalidHeaderValue($headerValue) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withAddedHeader('X-Foo', $headerValue);
    }

    /**
     * @dataProvider invalidHeaderValueProvider
     *
     * @return void
     */
    public function testAddInvalidHeaderValueItem($headerValue) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withAddedHeader('X-Foo', ['bar', $headerValue, 'baz']);
    }

    /**
     * @return void
     */
    public function testDeleteHeader() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');
        $copy = $mess->withoutHeader('X-Foo');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);
        $this->assertSame(['X-Foo' => ['bar']], $mess->getHeaders());
        $this->assertSame([], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testDeleteHeaderCaseInsensitive() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', 'bar')
            ->withoutHeader('X-Foo');

        $this->assertSame([], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testReplaceHeader() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');
        $copy = $mess->withHeader('X-Foo', 'baz');

        $this->assertSame(['X-Foo' => ['bar']], $mess->getHeaders());
        $this->assertSame(['X-Foo' => ['baz']], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testReplaceHeaderCaseInsensitive() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', 'bar')
            ->withHeader('X-Foo', 'baz');

        $this->assertSame(['X-Foo' => ['baz']], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testHasHeader() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');

        $this->assertTrue($mess->hasHeader('X-Foo'));
        $this->assertFalse($mess->hasHeader('X-Bar'));
    }

    /**
     * @return void
     */
    public function testHasHeaderCaseInsensitive() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertTrue($mess->hasHeader('x-foo'));
        $this->assertTrue($mess->hasHeader('X-Foo'));
        $this->assertTrue($mess->hasHeader('X-FOO'));
    }

    /**
     * @return void
     */
    public function testGetHeader() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');

        $this->assertSame(['bar'], $mess->getHeader('X-Foo'));
        $this->assertSame([], $mess->getHeader('X-Bar'));
    }

    /**
     * @return void
     */
    public function testGetHeaderCaseInsensitive() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertSame(['bar'], $mess->getHeader('x-foo'));
        $this->assertSame(['bar'], $mess->getHeader('X-Foo'));
        $this->assertSame(['bar'], $mess->getHeader('X-FOO'));
    }

    /**
     * @return void
     */
    public function testGetHeaderWithSeveralValues() : void
    {
        $mess = (new Message)->withHeader('X-Foo', ['bar', 'baz', 'quux']);

        $this->assertSame(['bar', 'baz', 'quux'], $mess->getHeader('X-Foo'));
    }

    /**
     * @return void
     */
    public function testGetHeaderLine() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');

        $this->assertSame('bar', $mess->getHeaderLine('X-Foo'));
        $this->assertSame('', $mess->getHeaderLine('X-Bar'));
    }

    /**
     * @return void
     */
    public function testGetHeaderLineCaseInsensitive() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertSame('bar', $mess->getHeaderLine('x-foo'));
        $this->assertSame('bar', $mess->getHeaderLine('X-Foo'));
        $this->assertSame('bar', $mess->getHeaderLine('X-FOO'));
    }

    /**
     * @return void
     */
    public function testGetHeaderLineWithSeveralValues() : void
    {
        $mess = (new Message)->withHeader('X-Foo', ['bar', 'baz', 'quux']);

        $this->assertSame('bar, baz, quux', $mess->getHeaderLine('X-Foo'));
    }

    /**
     * @return void
     */
    public function testBody() : void
    {
        $body = (new StreamFactory)->createStreamFromResource(\STDOUT);
        $mess = new Message();
        $copy = $mess->withBody($body);

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);

        // default value
        $this->assertNotSame($body, $mess->getBody());
        // assigned value
        $this->assertSame($body, $copy->getBody());
    }

    // Providers...

    /**
     * @return array
     */
    public function invalidProtocolVersionProvider() : array
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
            [function () {
            }],
        ];
    }

    /**
     * @return array
     */
    public function invalidHeaderNameProvider() : array
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
            [function () {
            }],
        ];
    }

    /**
     * @return array
     */
    public function invalidHeaderValueProvider() : array
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
            [function () {
            }],

            [[true]],
            [[false]],
            [[1]],
            [[1.1]],
            [[[]]],
            [[new \stdClass]],
            [[\STDOUT]],
            [[null]],
            [[function () {
            }]],
        ];
    }
}
