<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\StreamFactory;

use function fclose;
use function fopen;

class StreamFactoryTest extends TestCase
{
    public function testContracts(): void
    {
        $factory = new StreamFactory();

        $this->assertInstanceOf(StreamFactoryInterface::class, $factory);
    }

    public function testCreateStream(): void
    {
        $stream = (new StreamFactory)->createStream();
        $this->assertStringStartsWith('php://temp', $stream->getMetadata('uri'));
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
    }

    public function testCreateStreamWithContent(): void
    {
        $stream = (new StreamFactory)->createStream('foo');
        $this->assertSame(0, $stream->tell());
        $this->assertSame('foo', $stream->getContents());
    }

    public function testCreateStreamFromFile(): void
    {
        $stream = (new StreamFactory)->createStreamFromFile('php://memory');
        $this->assertSame('php://memory', $stream->getMetadata('uri'));
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
    }

    public function testCreateStreamFromFileWithMode(): void
    {
        $stream = (new StreamFactory)->createStreamFromFile('php://memory', 'r+');
        $this->assertSame('php://memory', $stream->getMetadata('uri'));
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
    }

    public function testCreateStreamFromInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unable to open the file "/55EF8096-7A6A-4C85-9BCD-6A5958376AB8" in the mode "r"'
        );

        (new StreamFactory)->createStreamFromFile('/55EF8096-7A6A-4C85-9BCD-6A5958376AB8', 'r');
    }

    public function testCreateStreamFromResource(): void
    {
        $resource = fopen('php://memory', 'r+b');
        $stream = (new StreamFactory)->createStreamFromResource($resource);
        $this->assertSame($resource, $stream->detach());
        fclose($resource);
    }

    public function testCreateStreamFromInvalidResource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected stream resource');

        (new StreamFactory)->createStreamFromResource(null);
    }
}
