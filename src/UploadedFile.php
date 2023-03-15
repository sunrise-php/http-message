<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import classes
 */
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\Stream\FileStream;

/**
 * Import functions
 */
use function dirname;
use function is_dir;
use function is_file;
use function is_writable;
use function sprintf;
use function unlink;

/**
 * Import constants
 */
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_PARTIAL;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;

/**
 * UploadedFile
 *
 * @link https://www.php-fig.org/psr/psr-7/
 */
class UploadedFile implements UploadedFileInterface
{

    /**
     * List of upload errors
     *
     * @link https://www.php.net/manual/en/features.file-upload.errors.php
     *
     * @var array<int, string>
     */
    public const UPLOAD_ERRORS = [
        UPLOAD_ERR_OK         => 'No error',
        UPLOAD_ERR_INI_SIZE   => 'Uploaded file exceeds the upload_max_filesize directive in the php.ini',
        UPLOAD_ERR_FORM_SIZE  => 'Uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
        UPLOAD_ERR_PARTIAL    => 'Uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary directory',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION  => 'File upload was stopped by a PHP extension',
    ];

    /**
     * The file stream
     *
     * @var StreamInterface|null
     */
    private ?StreamInterface $stream = null;

    /**
     * The file size
     *
     * @var int|null
     */
    private ?int $size;

    /**
     * The file's error code
     *
     * @var int
     */
    private int $errorCode;

    /**
     * The file's error message
     *
     * @var string
     */
    private string $errorMessage;

    /**
     * The client's file name
     *
     * @var string|null
     */
    private ?string $clientFilename;

    /**
     * The client's file media type
     *
     * @var string|null
     */
    private ?string $clientMediaType;

    /**
     * Constructor of the class
     *
     * @param StreamInterface|null $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(
        ?StreamInterface $stream,
        ?int $size = null,
        int $error = UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        if (UPLOAD_ERR_OK === $error) {
            $this->stream = $stream;
        }

        $this->size = $size;
        $this->errorCode = $error;
        $this->errorMessage = self::UPLOAD_ERRORS[$error] ?? 'Unknown error';
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * Gets the file stream
     *
     * @return StreamInterface
     *
     * @throws RuntimeException
     *         - If the file has no a stream due to an error;
     *         - If the file was already moved.
     */
    public function getStream(): StreamInterface
    {
        if (UPLOAD_ERR_OK <> $this->errorCode) {
            throw new RuntimeException(sprintf(
                'Uploaded file has no a stream due to the error #%d (%s)',
                $this->errorCode,
                $this->errorMessage
            ));
        }

        if (!isset($this->stream)) {
            throw new RuntimeException(
                'Uploaded file has no a stream because it was already moved'
            );
        }

        return $this->stream;
    }

    /**
     * Moves the file to the given path
     *
     * @param string $targetPath
     *
     * @return void
     *
     * @throws RuntimeException
     *         - If the file has no a stream due to an error;
     *         - If the file was already moved;
     *         - If the file cannot be read;
     *         - If the target path cannot be used.
     */
    public function moveTo($targetPath): void
    {
        if (UPLOAD_ERR_OK <> $this->errorCode) {
            throw new RuntimeException(sprintf(
                'Uploaded file cannot be moved due to the error #%d (%s)',
                $this->errorCode,
                $this->errorMessage
            ));
        }

        if (!isset($this->stream)) {
            throw new RuntimeException(
                'Uploaded file cannot be moved because it was already moved'
            );
        }

        if (!$this->stream->isReadable()) {
            throw new RuntimeException(
                'Uploaded file cannot be moved because it is not readable'
            );
        }

        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            throw new RuntimeException(sprintf(
                'Uploaded file cannot be moved because the directory "%s" is not writable',
                $targetDir
            ));
        }

        $targetStream = new FileStream($targetPath, 'wb');

        if ($this->stream->isSeekable()) {
            $this->stream->rewind();
        }

        while (!$this->stream->eof()) {
            $targetStream->write(
                $this->stream->read(4096)
            );
        }

        $targetStream->close();

        /** @var string|null */
        $sourcePath = $this->stream->getMetadata('uri');

        $this->stream->close();
        $this->stream = null;

        if (isset($sourcePath) && is_file($sourcePath)) {
            $sourceDir = dirname($sourcePath);
            if (is_writable($sourceDir)) {
                unlink($sourcePath);
            }
        }
    }

    /**
     * Gets the file size
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Gets the file's error code
     *
     * @return int
     */
    public function getError(): int
    {
        return $this->errorCode;
    }

    /**
     * Gets the client's file name
     *
     * @return string|null
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * Gets the client's file media type
     *
     * @return string|null
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
