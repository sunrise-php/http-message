<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

abstract class BaseMessageTest extends TestCase
{
    abstract protected function createSubject(): MessageInterface;

    protected function createSubjectWithProtocolVersion(string $protocolVersion): ?MessageInterface
    {
        return null;
    }

    protected function createSubjectWithHeaders(array $headers): ?MessageInterface
    {
        return null;
    }

    protected function createSubjectWithBody(StreamInterface $body): ?MessageInterface
    {
        return null;
    }

    public function testDefaultProtocolVersion(): void
    {
        $subject = $this->createSubject();

        $this->assertSame('1.1', $subject->getProtocolVersion());
    }

    public function testDefaultHeaders(): void
    {
        $subject = $this->createSubject();

        $this->assertSame([], $subject->getHeaders());
    }

    public function testDefaultBody(): void
    {
        $subject = $this->createSubject();
        $body = $subject->getBody();

        $this->assertSame('php://temp/maxmemory:2097152', $body->getMetadata('uri'));
        $this->assertTrue($body->isSeekable());
        $this->assertTrue($body->isReadable());
        $this->assertTrue($body->isWritable());
        $this->assertSame(0, $body->getSize());
    }

    /**
     * @dataProvider protocolVersionProvider
     */
    public function testSetProtocolVersion($protocolVersion): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withProtocolVersion($protocolVersion);

        $this->assertNotSame($clone, $subject);
        $this->assertSame($protocolVersion, $clone->getProtocolVersion());
        $this->assertSame('1.1', $subject->getProtocolVersion());
    }

    public function testSetProtocolVersionAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP version must be a string');

        $this->createSubject()->withProtocolVersion(null);
    }

    public function testSetProtocolVersionAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP version must be a string');

        $this->createSubject()->withProtocolVersion(1.1);
    }

    public function testSetProtocolVersionAEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP version cannot be an empty');

        $this->createSubject()->withProtocolVersion('');
    }

    /**
     * @dataProvider invalidProtocolVersionProvider
     */
    public function testSetInvalidProtocolVersion($protocolVersion): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP version is invalid');

        $this->createSubject()->withProtocolVersion($protocolVersion);
    }

    public function testSetHeader(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withHeader('X-Foo', 'bar');

        $this->assertNotSame($clone, $subject);

        $this->assertSame([
            'X-Foo' => ['bar'],
        ], $clone->getHeaders());

        $this->assertSame([], $subject->getHeaders());
    }

    public function testSetHeaderWithSeveralValues(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', ['bar', 'baz']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
        ], $subject->getHeaders());
    }

    public function testSetSeveralHeaders(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', 'bar')
            ->withHeader('X-Bar', 'baz');

        $this->assertSame([
            'X-Foo' => ['bar'],
            'X-Bar' => ['baz'],
        ], $subject->getHeaders());
    }

    public function testSetSeveralHeadersWithSeveralValues(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', ['bar', 'baz'])
            ->withHeader('X-Bar', ['baz', 'bat']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
            'X-Bar' => ['baz', 'bat'],
        ], $subject->getHeaders());
    }

    public function testSetHeaderWithEmptyValue(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', '');

        $this->assertSame([
            'X-Foo' => [''],
        ], $subject->getHeaders());
    }

    public function testSetHeaderWithEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name cannot be an empty');

        $this->createSubject()->withHeader('', 'bar');
    }

    public function testSetHeaderWithNameAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name must be a string');

        $this->createSubject()->withHeader(null, 'foo');
    }

    public function testSetHeaderWithNameAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name must be a string');

        $this->createSubject()->withHeader(42, 'foo');
    }

    public function testSetHeaderWithInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name is invalid');

        $this->createSubject()->withHeader('X-Foo:', 'bar');
    }

    public function testSetHeaderWithValueAsEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo cannot be an empty array');

        $this->createSubject()->withHeader('X-Foo', []);
    }

    public function testSetHeaderWithValueAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 must be a string');

        $this->createSubject()->withHeader('X-Foo', null);
    }

    public function testSetHeaderWithValueAsNullAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 must be a string');

        $this->createSubject()->withHeader('X-Foo', ['bar', null, 'baz']);
    }

    public function testSetHeaderWithValueAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 must be a string');

        $this->createSubject()->withHeader('X-Foo', 42);
    }

    public function testSetHeaderWithValueAsNumberAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 must be a string');

        $this->createSubject()->withHeader('X-Foo', ['bar', 42, 'baz']);
    }

    public function testSetHeaderWithInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 is invalid');

        $this->createSubject()->withHeader('X-Foo', "\0");
    }

    public function testSetHeaderWithInvalidValueAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 is invalid');

        $this->createSubject()->withHeader('X-Foo', ['bar', "\0", 'baz']);
    }

    public function testAddHeader(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');
        $clone = $subject->withAddedHeader('X-Foo', 'baz');

        $this->assertNotSame($clone, $subject);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
        ], $clone->getHeaders());

        $this->assertSame([
            'X-Foo' => ['bar'],
        ], $subject->getHeaders());
    }

    public function testAddHeaderWithSeveralValues(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', 'bar')
            ->withAddedHeader('X-Foo', ['baz', 'bat']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz', 'bat'],
        ], $subject->getHeaders());
    }

    public function testAddSeveralHeaders(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', 'bar')
            ->withAddedHeader('X-Foo', 'baz')
            ->withAddedHeader('X-Foo', 'bat');

        $this->assertSame([
            'X-Foo' => ['bar', 'baz', 'bat'],
        ], $subject->getHeaders());
    }

    public function testAddSeveralHeadersWithSeveralValues(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', 'bar')
            ->withAddedHeader('X-Foo', ['baz', 'bat'])
            ->withAddedHeader('X-Foo', ['qux', 'qaz']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz', 'bat', 'qux', 'qaz'],
        ], $subject->getHeaders());
    }

    public function testAddHeaderWithEmptyValue(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');
        $clone = $subject->withAddedHeader('X-Foo', '');

        $this->assertSame([
            'X-Foo' => ['bar', ''],
        ], $clone->getHeaders());

        $this->assertSame([
            'X-Foo' => ['bar'],
        ], $subject->getHeaders());
    }

    public function testAddHeaderWithEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name cannot be an empty');

        $this->createSubject()->withAddedHeader('', 'bar');
    }

    public function testAddHeaderWithNameAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name must be a string');

        $this->createSubject()->withAddedHeader(null, 'foo');
    }

    public function testAddHeaderWithNameAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name must be a string');

        $this->createSubject()->withAddedHeader(42, 'foo');
    }

    public function testAddHeaderWithInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name is invalid');

        $this->createSubject()->withAddedHeader('X-Foo:', 'bar');
    }

    public function testAddHeaderWithValueAsEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo cannot be an empty array');

        $this->createSubject()->withAddedHeader('X-Foo', []);
    }

    public function testAddHeaderWithValueAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 must be a string');

        $this->createSubject()->withAddedHeader('X-Foo', null);
    }

    public function testAddHeaderWithValueAsNullAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 must be a string');

        $this->createSubject()->withAddedHeader('X-Foo', ['bar', null, 'baz']);
    }

    public function testAddHeaderWithValueAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 must be a string');

        $this->createSubject()->withAddedHeader('X-Foo', 42);
    }

    public function testAddHeaderWithValueAsNumberAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 must be a string');

        $this->createSubject()->withAddedHeader('X-Foo', ['bar', 42, 'baz']);
    }

    public function testAddHeaderWithInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 is invalid');

        $this->createSubject()->withAddedHeader('X-Foo', "\0");
    }

    public function testAddHeaderWithInvalidValueAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 is invalid');

        $this->createSubject()->withAddedHeader('X-Foo', ['bar', "\0", 'baz']);
    }

    public function testAddNewHeader(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withAddedHeader('X-Foo', 'bar');

        $this->assertSame([
            'X-Foo' => ['bar'],
        ], $clone->getHeaders());

        $this->assertSame([], $subject->getHeaders());
    }

    public function testAddNewHeaderWithSeveralValues(): void
    {
        $subject = $this->createSubject()
            ->withAddedHeader('X-Foo', ['bar', 'baz']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
        ], $subject->getHeaders());
    }

    public function testAddNewSeveralHeaders(): void
    {
        $subject = $this->createSubject()
            ->withAddedHeader('X-Foo', 'bar')
            ->withAddedHeader('X-Foo', 'baz');

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
        ], $subject->getHeaders());
    }

    public function testAddNewSeveralHeadersWithSeveralValues(): void
    {
        $subject = $this->createSubject()
            ->withAddedHeader('X-Foo', ['bar', 'baz'])
            ->withAddedHeader('X-Foo', ['bat', 'qux']);

        $this->assertSame([
            'X-Foo' => ['bar', 'baz', 'bat', 'qux'],
        ], $subject->getHeaders());
    }

    public function testAddNewHeaderWithEmptyValue(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withAddedHeader('X-Foo', '');

        $this->assertSame([
            'X-Foo' => [''],
        ], $clone->getHeaders());

        $this->assertSame([], $subject->getHeaders());
    }

    public function testAddHeaderCaseInsensitive(): void
    {
        $subject = $this->createSubject()->withHeader('x-foo', 'bar');
        $clone = $subject->withAddedHeader('X-Foo', 'baz');

        $this->assertSame([
            'x-foo' => ['bar', 'baz'],
        ], $clone->getHeaders());

        $this->assertSame([
            'x-foo' => ['bar'],
        ], $subject->getHeaders());
    }

    public function testAddHeaderWithSeveralValuesCaseInsensitive(): void
    {
        $subject = $this->createSubject()
            ->withHeader('x-foo', 'bar')
            ->withAddedHeader('X-Foo', ['baz', 'bat']);

        $this->assertSame([
            'x-foo' => ['bar', 'baz', 'bat'],
        ], $subject->getHeaders());
    }

    public function testAddSeveralHeadersCaseInsensitive(): void
    {
        $subject = $this->createSubject()
            ->withHeader('x-foo', 'bar')
            ->withAddedHeader('X-Foo', 'baz')
            ->withAddedHeader('X-FOO', 'bat');

        $this->assertSame([
            'x-foo' => ['bar', 'baz', 'bat'],
        ], $subject->getHeaders());
    }

    public function testAddSeveralHeadersWithSeveralValuesCaseInsensitive(): void
    {
        $subject = $this->createSubject()
            ->withHeader('x-foo', 'bar')
            ->withAddedHeader('X-Foo', ['baz', 'bat'])
            ->withAddedHeader('X-Foo', ['qux', 'qaz']);

        $this->assertSame([
            'x-foo' => ['bar', 'baz', 'bat', 'qux', 'qaz'],
        ], $subject->getHeaders());
    }

    public function testReplaceHeader(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', 'bar')
            ->withHeader('X-Foo', 'baz');

        $this->assertSame([
            'X-Foo' => ['baz'],
        ], $subject->getHeaders());
    }

    public function testReplaceHeaderCaseInsensitive(): void
    {
        $subject = $this->createSubject()
            ->withHeader('X-Foo', 'bar')
            ->withHeader('x-foo', 'baz');

        $this->assertSame([
            'x-foo' => ['baz'],
        ], $subject->getHeaders());
    }

    public function testDeleteHeader(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');
        $clone = $subject->withoutHeader('X-Foo');

        $this->assertNotSame($clone, $subject);

        $this->assertSame([], $clone->getHeaders());

        $this->assertSame([
            'X-Foo' => ['bar'],
        ], $subject->getHeaders());
    }

    public function testDeleteHeaderCaseInsensitive(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');
        $clone = $subject->withoutHeader('x-foo');

        $this->assertSame([], $clone->getHeaders());

        $this->assertSame([
            'X-Foo' => ['bar'],
        ], $subject->getHeaders());
    }

    public function testHasHeader(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');

        $this->assertTrue($subject->hasHeader('X-Foo'));
        $this->assertFalse($subject->hasHeader('X-Bar'));
    }

    public function testHasHeaderCaseInsensitive(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');

        $this->assertTrue($subject->hasHeader('x-foo'));
        $this->assertTrue($subject->hasHeader('X-FOO'));
    }

    public function testGetHeader(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');

        $this->assertSame(['bar'], $subject->getHeader('X-Foo'));
        $this->assertSame([], $subject->getHeader('X-Bar'));
    }

    public function testGetHeaderCaseInsensitive(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');

        $this->assertSame(['bar'], $subject->getHeader('x-foo'));
        $this->assertSame(['bar'], $subject->getHeader('X-FOO'));
    }

    public function testGetHeaderWithSeveralValues(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', ['bar', 'baz', 'bat']);

        $this->assertSame(['bar', 'baz', 'bat'], $subject->getHeader('X-Foo'));
    }

    public function testGetHeaderLine(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');

        $this->assertSame('bar', $subject->getHeaderLine('X-Foo'));
        $this->assertSame('', $subject->getHeaderLine('X-Bar'));
    }

    public function testGetHeaderLineCaseInsensitive(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', 'bar');

        $this->assertSame('bar', $subject->getHeaderLine('x-foo'));
        $this->assertSame('bar', $subject->getHeaderLine('X-FOO'));
    }

    public function testGetHeaderLineWithSeveralValues(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', ['bar', 'baz']);

        $this->assertSame('bar,baz', $subject->getHeaderLine('X-Foo'));
    }

    public function testGetHeaderLineWithEmptyValue(): void
    {
        $subject = $this->createSubject()->withHeader('X-Foo', '');

        $this->assertSame('', $subject->getHeaderLine('X-Foo'));
    }

    public function testSetBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $subject = $this->createSubject();
        $clone = $subject->withBody($body);

        $this->assertNotSame($clone, $subject);
        $this->assertSame($body, $clone->getBody());
        $this->assertNotSame($body, $subject->getBody());
    }

    /**
     * @dataProvider protocolVersionProvider
     */
    public function testConstructorWithProtocolVersion(string $protocolVersion): void
    {
        $subject = $this->createSubjectWithProtocolVersion($protocolVersion);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame($protocolVersion, $subject->getProtocolVersion());
    }

    /**
     * @dataProvider invalidProtocolVersionProvider
     */
    public function testConstructorWithInvalidProtocolVersion(string $protocolVersion): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP version is invalid');

        $subject = $this->createSubjectWithProtocolVersion($protocolVersion);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeaders(): void
    {
        $subject = $this->createSubjectWithHeaders(['X-Foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['X-Foo' => ['bar']], $subject->getHeaders());
    }

    public function testConstructorWithHeadersWithSeveralValues(): void
    {
        $subject = $this->createSubjectWithHeaders(['X-Foo' => ['bar', 'baz']]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['X-Foo' => ['bar', 'baz']], $subject->getHeaders());
    }

    public function testConstructorWithSeveralHeaders(): void
    {
        $subject = $this->createSubjectWithHeaders([
            'X-Foo' => 'bar',
            'X-Bar' => 'baz',
        ]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame([
            'X-Foo' => ['bar'],
            'X-Bar' => ['baz'],
        ], $subject->getHeaders());
    }

    public function testConstructorWithSeveralHeadersWithSeveralValues(): void
    {
        $subject = $this->createSubjectWithHeaders([
            'X-Foo' => ['bar', 'baz'],
            'X-Bar' => ['baz', 'bat'],
        ]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
            'X-Bar' => ['baz', 'bat'],
        ], $subject->getHeaders());
    }

    public function testConstructorWithSeveralHeadersWithSameName(): void
    {
        $subject = $this->createSubjectWithHeaders([
            'X-Foo' => 'bar',
            'x-foo' => 'baz',
        ]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame([
            'X-Foo' => ['bar', 'baz'],
        ], $subject->getHeaders());
    }

    public function testConstructorWithHeadersWithEmptyValue(): void
    {
        $subject = $this->createSubjectWithHeaders(['X-Foo' => '']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['X-Foo' => ['']], $subject->getHeaders());
    }

    public function testConstructorWithHeadersWithEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name cannot be an empty');

        $subject = $this->createSubjectWithHeaders(['' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithNameAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name must be a string');

        $subject = $this->createSubjectWithHeaders([42 => 'foo']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP header name is invalid');

        $subject = $this->createSubjectWithHeaders(['X-Foo:' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithValueAsEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo cannot be an empty array');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => []]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithValueAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 must be a string');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => null]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithValueAsNullAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 must be a string');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => ['bar', null, 'baz']]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithValueAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 must be a string');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => 42]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithValueAsNumberAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 must be a string');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => ['bar', 42, 'baz']]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:0 is invalid');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => "\0"]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithHeadersWithInvalidValueAmongOthers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header X-Foo:1 is invalid');

        $subject = $this->createSubjectWithHeaders(['X-Foo' => ['bar', "\0", 'baz']]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $subject = $this->createSubjectWithBody($body);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame($body, $subject->getBody());
    }

    public function protocolVersionProvider(): array
    {
        return [
            ['1.0'],
            ['1.1'],
            ['2.0'],
            ['2'],
        ];
    }

    public function invalidProtocolVersionProvider(): array
    {
        return [
            ['.'],
            ['1.'],
            ['.1'],
            ['1.1.'],
            ['.1.1'],
            [' 1.1'],
            ['1.1 '],
            ['1.1.1'],
            ['-1.1'],
            ['a'],
            ['a.'],
            ['.a'],
            ['a.a'],
            ['HTTP/1.1'],
            // ['2.1'],
            // ['3'],
        ];
    }
}
