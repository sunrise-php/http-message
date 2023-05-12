<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\Stream;

use function fclose;
use function fopen;
use function is_resource;
use function stream_get_meta_data;

use const STDIN;
use const STDOUT;

class StreamTest extends TestCase
{
    private $testResource = null;
    private ?Stream $testStream = null;

    protected function setUp(): void
    {
        $this->testResource = fopen('php://memory', 'r+b');
        $this->testStream = new Stream($this->testResource);
    }

    protected function tearDown(): void
    {
        if (isset($this->testStream)) {
            $this->testStream->close();
        }

        if (is_resource($this->testResource)) {
            fclose($this->testResource);
        }
    }

    public function testCreateWithStream(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $this->assertSame($stream, Stream::create($stream));
    }

    public function testCreateWithResource(): void
    {
        $this->assertSame($this->testResource, Stream::create($this->testResource)->detach());
    }

    public function testCreateWithUnexpectedOperand(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected stream resource');

        Stream::create(null);
    }

    public function testContracts(): void
    {
        $this->assertInstanceOf(StreamInterface::class, $this->testStream);
    }

    public function testConstructorWithClosedResource(): void
    {
        fclose($this->testResource);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected stream resource');

        new Stream($this->testResource);
    }

    public function testConstructorWithNotResource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected stream resource');

        new Stream(null);
    }

    public function testAutoClose(): void
    {
        $this->testStream = null;

        $this->assertIsClosedResource($this->testResource);
    }

    public function testAutoCloseDisabled(): void
    {
        // no object references, destruction must occur immediately...
        new Stream($this->testResource, false);

        $this->assertTrue(is_resource($this->testResource));
    }

    public function testDetach(): void
    {
        $this->assertSame($this->testResource, $this->testStream->detach());
        $this->assertNull($this->testStream->detach());
    }

    public function testClose(): void
    {
        $this->testStream->close();
        $this->assertIsClosedResource($this->testResource);
        $this->assertNull($this->testStream->detach());
    }

    public function testEof(): void
    {
        $this->assertFalse($this->testStream->eof());

        while (!$this->testStream->eof()) {
            $this->testStream->read(4096);
        }

        $this->assertTrue($this->testStream->eof());
    }

    public function testEofAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertTrue($this->testStream->eof());
    }

    public function testEofAfterClose(): void
    {
        $this->testStream->close();
        $this->assertTrue($this->testStream->eof());
    }

    public function testTell(): void
    {
        $this->assertSame(0, $this->testStream->tell());
        $this->testStream->write('foo');
        $this->assertSame(3, $this->testStream->tell());
        $this->testStream->seek(1);
        $this->assertSame(1, $this->testStream->tell());
        $this->testStream->rewind();
        $this->assertSame(0, $this->testStream->tell());
    }

    public function testTellAfterDetach(): void
    {
        $this->testStream->detach();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->tell();
    }

    public function testTellAfterClose(): void
    {
        $this->testStream->close();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->tell();
    }

    public function testFailedTell(): void
    {
        $testStream = new Stream(STDIN, false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get the stream pointer position');

        $testStream->tell();
    }

    public function testIsSeekable(): void
    {
        $this->assertTrue($this->testStream->isSeekable());
    }

    public function testIsSeekableUnseekableResource(): void
    {
        $testStream = new Stream(STDIN, false);
        $this->assertFalse($testStream->isSeekable());
    }

    public function testIsSeekableAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertFalse($this->testStream->isSeekable());
    }

    public function testIsSeekableAfterClose(): void
    {
        $this->testStream->close();
        $this->assertFalse($this->testStream->isSeekable());
    }

    public function testRewind(): void
    {
        $this->testStream->write('foo');
        $this->assertSame(3, $this->testStream->tell());
        $this->testStream->rewind();
        $this->assertSame(0, $this->testStream->tell());
    }

    public function testRewindAfterDetach(): void
    {
        $this->testStream->detach();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->rewind();
    }

    public function testRewindAfterClose(): void
    {
        $this->testStream->close();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->rewind();
    }

    public function testRewindInUnseekableResource(): void
    {
        $testStream = new Stream(STDIN, false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not seekable');

        $testStream->rewind();
    }

    public function testSeek(): void
    {
        $this->testStream->write('foo');
        $this->testStream->seek(1);
        $this->assertSame(1, $this->testStream->tell());
    }

    public function testSeekAfterDetach(): void
    {
        $this->testStream->detach();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->seek(0);
    }

    public function testSeekAfterClose(): void
    {
        $this->testStream->close();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->seek(0);
    }

    public function testSeekInUnseekableResource(): void
    {
        $testStream = new Stream(STDIN, false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not seekable');

        $testStream->seek(0);
    }

    public function testFailedSeek(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to move the stream pointer position');

        $this->testStream->seek(1);
    }

    public function testIsWritable(): void
    {
        $this->assertTrue($this->testStream->isWritable());
    }

    public function testIsWritableUnwritableResource(): void
    {
        $testStream = new Stream(STDIN, false);
        $this->assertFalse($testStream->isWritable());
    }

    public function testIsWritableAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertFalse($this->testStream->isWritable());
    }

    public function testIsWritableAfterClose(): void
    {
        $this->testStream->close();
        $this->assertFalse($this->testStream->isWritable());
    }

    public function testWrite(): void
    {
        $this->assertSame(3, $this->testStream->write('foo'));
        $this->testStream->rewind();
        $this->assertSame('foo', $this->testStream->read(3));
    }

    public function testWriteAfterDetach(): void
    {
        $this->testStream->detach();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->write('foo');
    }

    public function testWriteAfterClose(): void
    {
        $this->testStream->close();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->write('foo');
    }

    public function testWriteToUnwritableResource(): void
    {
        $testStream = new Stream(STDIN, false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not writable');

        $testStream->write('foo');
    }

    public function testIsReadable(): void
    {
        $this->assertTrue($this->testStream->isReadable());
    }

    public function testIsReadableUnreadableResource(): void
    {
        $testStream = new Stream(STDOUT, false);
        $this->assertFalse($testStream->isReadable());
    }

    public function testIsReadableAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertFalse($this->testStream->isReadable());
    }

    public function testIsReadableAfterClose(): void
    {
        $this->testStream->close();
        $this->assertFalse($this->testStream->isReadable());
    }

    public function testRead(): void
    {
        $this->testStream->write('foo');
        $this->testStream->rewind();
        $this->assertSame('foo', $this->testStream->read(3));
    }

    public function testReadAfterDetach(): void
    {
        $this->testStream->detach();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->read(1);
    }

    public function testReadAfterClose(): void
    {
        $this->testStream->close();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->read(1);
    }

    public function testReadFromUnreadableResource(): void
    {
        $testStream = new Stream(STDOUT, false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not readable');

        $testStream->read(1);
    }

    public function testGetContents(): void
    {
        $this->testStream->write('foo');
        $this->testStream->rewind();
        $this->assertSame('foo', $this->testStream->getContents());
    }

    public function testGetContentsAfterDetach(): void
    {
        $this->testStream->detach();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->getContents();
    }

    public function testGetContentsAfterClose(): void
    {
        $this->testStream->close();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The stream does not have a resource, so the operation is not possible');

        $this->testStream->getContents();
    }

    public function testGetContentsFromUnreadableResource(): void
    {
        $testStream = new Stream(STDOUT, false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not readable');

        $testStream->getContents();
    }

    public function testGetMetaData(): void
    {
        $this->assertSame(
            stream_get_meta_data($this->testResource),
            $this->testStream->getMetadata()
        );
    }

    public function testGetMetaDataWithKey(): void
    {
        $this->assertSame(
            stream_get_meta_data($this->testResource)['uri'],
            $this->testStream->getMetadata('uri')
        );
    }

    public function testGetMetaDataWithUnknownKey(): void
    {
        $this->assertNull($this->testStream->getMetadata('unknown'));
    }

    public function testGetMetaDataAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertNull($this->testStream->getMetadata());
    }

    public function testGetMetaDataAfterClose(): void
    {
        $this->testStream->close();
        $this->assertNull($this->testStream->getMetadata());
    }

    public function testGetSize(): void
    {
        $this->assertSame($this->testStream->write('foo'), $this->testStream->getSize());
    }

    public function testGetSizeAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertNull($this->testStream->getSize());
    }

    public function testGetSizeAfterClose(): void
    {
        $this->testStream->close();
        $this->assertNull($this->testStream->getSize());
    }

    public function testStringify(): void
    {
        $this->testStream->write('foo');
        $this->assertSame('foo', $this->testStream->__toString());
    }

    public function testStringifyAfterDetach(): void
    {
        $this->testStream->detach();
        $this->assertSame('', $this->testStream->__toString());
    }

    public function testStringifyAfterClose(): void
    {
        $this->testStream->close();
        $this->assertSame('', $this->testStream->__toString());
    }

    public function testStringifyForInvalidResource(): void
    {
        $testStream = new Stream(STDOUT, false);
        $this->assertSame('', $testStream->__toString());
    }
}
