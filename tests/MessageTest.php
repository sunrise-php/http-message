<?php declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Sunrise\Http\Header\HeaderCollection;
use Sunrise\Http\Header\HeaderInterface;
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
    public function testConstructor() : void
    {
        $mess = new Message();

        $this->assertInstanceOf(MessageInterface::class, $mess);
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
        $this->assertEquals('1.1', $mess->getProtocolVersion());
        // assigned value
        $this->assertEquals('2.0', $copy->getProtocolVersion());
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
        $copy = $mess->withHeader('x-foo', 'bar');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);
        $this->assertEquals([], $mess->getHeaders());
        $this->assertEquals(['x-foo' => ['bar']], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetHeaderWithValueArray() : void
    {
        $mess = (new Message)->withHeader('x-foo', ['bar', 'baz']);

        $this->assertEquals(['x-foo' => ['bar', 'baz']], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetSeveralHeaders() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', ['bar', 'baz'])
            ->withHeader('x-quux', ['quuux', 'quuuux']);

        $this->assertEquals([
            'x-foo' => ['bar', 'baz'],
            'x-quux' => ['quuux', 'quuuux'],
        ], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetHeaderLowercase() : void
    {
        $mess = (new Message)->withHeader('X-Foo', 'bar');

        $this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
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

        (new Message)->withHeader('x-foo', $headerValue);
    }

    /**
     * @dataProvider invalidHeaderValueProvider
     *
     * @return void
     */
    public function testSetInvalidHeaderValueInArray($headerValue) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withHeader('x-foo', ['bar', $headerValue, 'baz']);
    }

    /**
     * @return void
     */
    public function testAddHeader() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');
        $copy = $mess->withAddedHeader('x-foo', 'baz');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);
        $this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
        $this->assertEquals(['x-foo' => ['bar', 'baz']], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testAddHeaderWithValueArray() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', ['bar', 'baz'])
            ->withAddedHeader('x-foo', ['quux', 'quuux']);

        $this->assertEquals(['x-foo' => ['bar', 'baz', 'quux', 'quuux']], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testAddSeveralHeaders() : void
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

    /**
     * @return void
     */
    public function testAddHeaderLowercase() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', 'bar')
            ->withAddedHeader('X-Foo', 'baz');

        $this->assertEquals(['x-foo' => ['bar', 'baz']], $mess->getHeaders());
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

        (new Message)->withAddedHeader('x-foo', $headerValue);
    }

    /**
     * @dataProvider invalidHeaderValueProvider
     *
     * @return void
     */
    public function testAddInvalidHeaderValueInArray($headerValue) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Message)->withAddedHeader('x-foo', ['bar', $headerValue, 'baz']);
    }

    /**
     * @return void
     */
    public function testWithMultipleHeaders() : void
    {
        $message = new Message();

        $subject = $message
            ->withMultipleHeaders(['x-foo' => 'bar'])
            ->withMultipleHeaders(['x-foo' => 'baz']);

        $this->assertNotSame($subject, $message);
        $this->assertCount(0, $message->getHeaders());
        $this->assertSame(['x-foo' => ['baz']], $subject->getHeaders());

        $subject = $message
            ->withMultipleHeaders(['x-foo' => 'bar'], true)
            ->withMultipleHeaders(['x-foo' => 'baz'], true);

        $this->assertNotSame($subject, $message);
        $this->assertCount(0, $message->getHeaders());
        $this->assertSame(['x-foo' => ['bar', 'baz']], $subject->getHeaders());
    }

    /**
     * @return void
     */
    public function testWithHeaderObject() : void
    {
        $foo1 = $this->createMock(HeaderInterface::class);
        $foo1->method('getFieldName')->willReturn('x-foo');
        $foo1->method('getFieldValue')->willReturn('1');

        $foo2 = $this->createMock(HeaderInterface::class);
        $foo2->method('getFieldName')->willReturn('x-foo');
        $foo2->method('getFieldValue')->willReturn('2');

        $mess0 = new Message();

        $copy1 = $mess0->withHeaderObject($foo1);
        $this->assertNotSame($copy1, $mess0);
        $this->assertEquals([], $mess0->getHeaders());
        $this->assertEquals(['x-foo' => ['1']], $copy1->getHeaders());

        $copy2 = $copy1->withHeaderObject($foo2);
        $this->assertNotSame($copy2, $copy1);
        $this->assertEquals(['x-foo' => ['1']], $copy1->getHeaders());
        $this->assertEquals(['x-foo' => ['2']], $copy2->getHeaders());

        $copy3 = $copy2->withHeaderObject($foo1, true);
        $this->assertNotSame($copy3, $copy2);
        $this->assertEquals(['x-foo' => ['2']], $copy2->getHeaders());
        $this->assertEquals(['x-foo' => ['2', '1']], $copy3->getHeaders());
    }

    /**
     * @return void
     */
    public function testWithHeaderCollection() : void
    {
        $foo1 = $this->createMock(HeaderInterface::class);
        $foo1->method('getFieldName')->willReturn('x-foo');
        $foo1->method('getFieldValue')->willReturn('1');

        $foo2 = $this->createMock(HeaderInterface::class);
        $foo2->method('getFieldName')->willReturn('x-foo');
        $foo2->method('getFieldValue')->willReturn('2');

        $bar1 = $this->createMock(HeaderInterface::class);
        $bar1->method('getFieldName')->willReturn('x-bar');
        $bar1->method('getFieldValue')->willReturn('1');

        $bar2 = $this->createMock(HeaderInterface::class);
        $bar2->method('getFieldName')->willReturn('x-bar');
        $bar2->method('getFieldValue')->willReturn('2');

        $coll1 = new HeaderCollection([$foo1, $bar1]);
        $coll2 = new HeaderCollection([$foo2, $bar2]);

        $mess0 = new Message();

        $copy1 = $mess0->withHeaderCollection($coll1);
        $this->assertNotSame($copy1, $mess0);
        $this->assertEquals([], $mess0->getHeaders());
        $this->assertEquals(['x-foo' => ['1'], 'x-bar' => ['1']], $copy1->getHeaders());

        $copy2 = $copy1->withHeaderCollection($coll2);
        $this->assertNotSame($copy2, $copy1);
        $this->assertEquals(['x-foo' => ['1'], 'x-bar' => ['1']], $copy1->getHeaders());
        $this->assertEquals(['x-foo' => ['2'], 'x-bar' => ['2']], $copy2->getHeaders());

        $copy3 = $copy2->withHeaderCollection($coll1, true);
        $this->assertNotSame($copy3, $copy2);
        $this->assertEquals(['x-foo' => ['2'], 'x-bar' => ['2']], $copy2->getHeaders());
        $this->assertEquals(['x-foo' => ['2', '1'], 'x-bar' => ['2', '1']], $copy3->getHeaders());
    }

    /**
     * @return void
     */
    public function testDeleteHeader() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');
        $copy = $mess->withoutHeader('x-foo');

        $this->assertInstanceOf(MessageInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);
        $this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
        $this->assertEquals([], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testDeleteHeaderCaseInsensitive() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', 'bar')
            ->withoutHeader('X-Foo');

        $this->assertEquals([], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testReplaceHeader() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');
        $copy = $mess->withHeader('x-foo', 'baz');

        $this->assertEquals(['x-foo' => ['bar']], $mess->getHeaders());
        $this->assertEquals(['x-foo' => ['baz']], $copy->getHeaders());
    }

    /**
     * @return void
     */
    public function testReplaceHeaderCaseInsensitive() : void
    {
        $mess = (new Message)
            ->withHeader('x-foo', 'bar')
            ->withHeader('X-Foo', 'baz');

        $this->assertEquals(['x-foo' => ['baz']], $mess->getHeaders());
    }

    /**
     * @return void
     */
    public function testHasHeader() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertTrue($mess->hasHeader('x-foo'));
        $this->assertFalse($mess->hasHeader('x-bar'));
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
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertEquals(['bar'], $mess->getHeader('x-foo'));
        $this->assertEquals([], $mess->getHeader('x-bar'));
    }

    /**
     * @return void
     */
    public function testGetHeaderCaseInsensitive() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertEquals(['bar'], $mess->getHeader('x-foo'));
        $this->assertEquals(['bar'], $mess->getHeader('X-Foo'));
        $this->assertEquals(['bar'], $mess->getHeader('X-FOO'));
    }

    /**
     * @return void
     */
    public function testGetHeaderWithMultipleValue() : void
    {
        $mess = (new Message)->withHeader('x-foo', ['bar', 'baz', 'quux']);

        $this->assertEquals(['bar', 'baz', 'quux'], $mess->getHeader('x-foo'));
    }

    /**
     * @return void
     */
    public function testGetHeaderLine() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertEquals('bar', $mess->getHeaderLine('x-foo'));
        $this->assertEquals('', $mess->getHeaderLine('x-bar'));
    }

    /**
     * @return void
     */
    public function testGetHeaderLineCaseInsensitive() : void
    {
        $mess = (new Message)->withHeader('x-foo', 'bar');

        $this->assertEquals('bar', $mess->getHeaderLine('x-foo'));
        $this->assertEquals('bar', $mess->getHeaderLine('X-Foo'));
        $this->assertEquals('bar', $mess->getHeaderLine('X-FOO'));
    }

    /**
     * @return void
     */
    public function testGetHeaderLineWithMultipleValue() : void
    {
        $mess = (new Message)->withHeader('x-foo', ['bar', 'baz', 'quux']);

        $this->assertEquals('bar, baz, quux', $mess->getHeaderLine('x-foo'));
    }

    /**
     * @return void
     */
    public function testBody() : void
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
