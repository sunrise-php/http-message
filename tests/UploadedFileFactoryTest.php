<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Sunrise\Http\Message\Stream\PhpTempStream;
use Sunrise\Http\Message\UploadedFileFactory;

use const UPLOAD_ERR_OK;

class UploadedFileFactoryTest extends TestCase
{
    public function testContracts() : void
    {
        $factory = new UploadedFileFactory();

        $this->assertInstanceOf(UploadedFileFactoryInterface::class, $factory);
    }

    public function testCreateUploadedFileWithAllParameters(): void
    {
        $stream = new PhpTempStream();
        $file = (new UploadedFileFactory)->createUploadedFile($stream, 0, UPLOAD_ERR_OK, 'foo', 'bar');

        $this->assertSame($stream, $file->getStream());
        $this->assertSame(0, $file->getSize());
        $this->assertSame(UPLOAD_ERR_OK, $file->getError());
        $this->assertSame('foo', $file->getClientFilename());
        $this->assertSame('bar', $file->getClientMediaType());
    }

    public function testCreateUploadedFileWithRequiredParametersOnly(): void
    {
        $stream = new PhpTempStream();
        $file = (new UploadedFileFactory)->createUploadedFile($stream);

        $this->assertSame($stream, $file->getStream());
        $this->assertNull($file->getSize());
        $this->assertSame(UPLOAD_ERR_OK, $file->getError());
        $this->assertNull($file->getClientFilename());
        $this->assertNull($file->getClientMediaType());
    }
}
