<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\FailedUploadedFileOperationException;
use Sunrise\Http\Message\Exception\InvalidUploadedFileException;
use Sunrise\Http\Message\Exception\InvalidUploadedFileOperationException;
use Sunrise\Http\Message\Stream\FileStream;
use Sunrise\Http\Message\Stream\PhpTempStream;
use Sunrise\Http\Message\Stream\TmpfileStream;
use Sunrise\Http\Message\UploadedFile;

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
        $file = new UploadedFile(new PhpTempStream());

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

    /**
     * @dataProvider uploadErrorCodeProvider
     */
    public function testGetsStreamWithError(int $errorCode): void
    {
        $file = new UploadedFile(new PhpTempStream(), null, $errorCode);

        $errorMessage = UploadedFile::UPLOAD_ERRORS[$errorCode] ?? UploadedFile::UNKNOWN_ERROR_TEXT;

        $this->expectException(InvalidUploadedFileException::class);
        $this->expectExceptionMessage(sprintf(
            'The uploaded file has no a stream due to the error #%d (%s)',
            $errorCode,
            $errorMessage
        ));

        $file->getStream();
    }

    public function testGetsStreamAfterMove(): void
    {
        $tmpfile = new TmpfileStream();

        $file = new UploadedFile(new PhpTempStream());
        $file->moveTo($tmpfile->getMetadata('uri'));

        $this->expectException(InvalidUploadedFileException::class);
        $this->expectExceptionMessage(
            'The uploaded file has no a stream because it was already moved'
        );

        $file->getStream();
    }

    public function testMove(): void
    {
        // will be deleted after the move
        $srcStream = FileStream::tempFile();
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

    /**
     * @dataProvider uploadErrorCodeProvider
     */
    public function testMoveWithError(int $errorCode): void
    {
        $file = new UploadedFile(new PhpTempStream(), null, $errorCode);

        $errorMessage = UploadedFile::UPLOAD_ERRORS[$errorCode] ?? UploadedFile::UNKNOWN_ERROR_TEXT;

        $this->expectException(InvalidUploadedFileException::class);
        $this->expectExceptionMessage(sprintf(
            'The uploaded file cannot be moved due to the error #%d (%s)',
            $errorCode,
            $errorMessage
        ));

        $file->moveTo('/foo');
    }

    public function testMoveAfterMove(): void
    {
        $tmpfile = new TmpfileStream();

        $file = new UploadedFile(new PhpTempStream());
        $file->moveTo($tmpfile->getMetadata('uri'));

        $this->expectException(InvalidUploadedFileException::class);
        $this->expectExceptionMessage(
            'The uploaded file cannot be moved because it was already moved'
        );

        $file->moveTo('/foo');
    }

    public function testMoveUnreadableFile(): void
    {
        $tmpfile = new TmpfileStream();

        $file = new UploadedFile(new FileStream($tmpfile->getMetadata('uri'), 'w'));

        $this->expectException(InvalidUploadedFileOperationException::class);
        $this->expectExceptionMessage(
            'The uploaded file cannot be moved because it is not readable'
        );

        $file->moveTo('/foo');
    }

    public function testMoveUnwritableDirectory(): void
    {
        $file = new UploadedFile(new PhpTempStream());

        $this->expectException(FailedUploadedFileOperationException::class);
        $this->expectExceptionMessage(
            'The uploaded file cannot be moved because ' .
            'the directory "/4c32dad5-181f-46b7-a86a-15568e11fdf9" is not writable'
        );

        $file->moveTo('/4c32dad5-181f-46b7-a86a-15568e11fdf9/foo');
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
