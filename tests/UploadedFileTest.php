<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\Stream\PhpTempStream;
use Sunrise\Http\Message\Stream\TempFileStream;
use Sunrise\Http\Message\Stream\TmpfileStream;
use Sunrise\Http\Message\UploadedFile;
use TypeError;

use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_PARTIAL;

class UploadedFileTest extends TestCase
{
    public function testContracts(): void
    {
        $file = new UploadedFile(null);

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
    }

    public function testConstructorWithAllParameters(): void
    {
        $stream = new PhpTempStream();
        $file = new UploadedFile($stream, 0, UPLOAD_ERR_OK, 'foo', 'bar');

        $this->assertSame($stream, $file->getStream());
        $this->assertSame(0, $file->getSize());
        $this->assertSame(UPLOAD_ERR_OK, $file->getError());
        $this->assertSame('foo', $file->getClientFilename());
        $this->assertSame('bar', $file->getClientMediaType());
    }

    public function testConstructorWithRequiredParametersOnly(): void
    {
        $stream = new PhpTempStream();
        $file = new UploadedFile($stream);

        $this->assertSame($stream, $file->getStream());
        $this->assertNull($file->getSize());
        $this->assertSame(UPLOAD_ERR_OK, $file->getError());
        $this->assertNull($file->getClientFilename());
        $this->assertNull($file->getClientMediaType());
    }

    public function testGetStreamWithoutStream(): void
    {
        $file = new UploadedFile(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Uploaded file has no stream');

        $file->getStream();
    }

    /**
     * @dataProvider uploadErrorCodeProvider
     */
    public function testGetStreamWithError(int $errorCode): void
    {
        $file = new UploadedFile(null, null, $errorCode);

        $expectedMessage = UploadedFile::UPLOAD_ERRORS[$errorCode] ?? 'Unknown file upload error';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode($errorCode);
        $this->expectExceptionMessage($expectedMessage);

        $file->getStream();
    }

    public function testGetStreamAfterMove(): void
    {
        $tmpfile = new TmpfileStream();
        $file = new UploadedFile(new TmpfileStream());
        $file->moveTo($tmpfile->getMetadata('uri'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Uploaded file was moved');

        $file->getStream();
    }

    public function testMove(): void
    {
        // will be deleted after the move
        $srcStream = new TempFileStream();
        $srcStream->write('foo');
        $srcPath = $srcStream->getMetadata('uri');

        // will be deleted automatically
        $destStream = new TmpfileStream();
        $destStream->write('bar');
        $destPath = $destStream->getMetadata('uri');

        $file = new UploadedFile($srcStream);
        $file->moveTo($destPath);
        $this->assertStringEqualsFile($destPath, 'foo');
        $this->assertFileDoesNotExist($srcPath);
    }

    public function testMoveWithoutStream(): void
    {
        $file = new UploadedFile(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Uploaded file has no stream');

        $file->moveTo('/');
    }

    /**
     * @dataProvider uploadErrorCodeProvider
     */
    public function testMoveWithError(int $errorCode): void
    {
        $file = new UploadedFile(null, null, $errorCode);

        $expectedMessage = UploadedFile::UPLOAD_ERRORS[$errorCode] ?? 'Unknown file upload error';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode($errorCode);
        $this->expectExceptionMessage($expectedMessage);

        $file->moveTo('/');
    }

    public function testMoveAfterMove(): void
    {
        $tmpfile = new TmpfileStream();

        $file = new UploadedFile(new TmpfileStream());
        $file->moveTo($tmpfile->getMetadata('uri'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Uploaded file was moved');

        $file->moveTo($tmpfile->getMetadata('uri'));
    }

    public function testMoveInvalidTargetPath(): void
    {
        $file = new UploadedFile(null);

        $this->expectException(TypeError::class);

        $file->moveTo(null);
    }

    public function testMoveNonFileStream(): void
    {
        $file = new UploadedFile(new PhpTempStream());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Uploaded file does not exist or is not readable');

        $file->moveTo('/');
    }

    public function testCloseStreamAfterMove(): void
    {
        $tmpfile = new TmpfileStream();

        $file = new UploadedFile($tmpfile);
        $file->moveTo($tmpfile->getMetadata('uri'));

        $this->assertNull($tmpfile->detach());
    }

    public function uploadErrorCodeProvider(): array
    {
        return [
            [UPLOAD_ERR_CANT_WRITE],
            [UPLOAD_ERR_EXTENSION],
            [UPLOAD_ERR_FORM_SIZE],
            [UPLOAD_ERR_INI_SIZE],
            [UPLOAD_ERR_NO_FILE],
            [UPLOAD_ERR_NO_TMP_DIR],
            [UPLOAD_ERR_PARTIAL],
            [-1], // unknown error...
        ];
    }
}
