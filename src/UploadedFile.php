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

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\RuntimeException;
use Throwable;
use TypeError;

use function dirname;
use function gettype;
use function is_dir;
use function is_file;
use function is_readable;
use function is_string;
use function is_uploaded_file;
use function is_writable;
use function move_uploaded_file;
use function rename;
use function sprintf;

use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_PARTIAL;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @link https://www.php.net/manual/en/features.file-upload.errors.php
     *
     * @var array<int, non-empty-string>
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

    private ?StreamInterface $stream;
    private ?int $size;
    private int $errorCode;
    private string $errorMessage;
    private ?string $clientFilename;
    private ?string $clientMediaType;
    private bool $isMoved = false;

    public function __construct(
        ?StreamInterface $stream,
        ?int $size = null,
        int $error = UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        $this->stream = $stream;
        $this->size = $size;
        $this->errorCode = $error;
        $this->errorMessage = self::UPLOAD_ERRORS[$error] ?? 'Unknown file upload error';
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * @inheritDoc
     */
    public function getStream(): StreamInterface
    {
        if ($this->isMoved) {
            throw new RuntimeException('Uploaded file was moved');
        }

        if ($this->errorCode !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->errorMessage, $this->errorCode);
        }

        if ($this->stream === null) {
            throw new RuntimeException('Uploaded file has no stream');
        }

        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function moveTo($targetPath): void
    {
        /** @psalm-suppress TypeDoesNotContainType */
        if (!is_string($targetPath)) {
            throw new TypeError(sprintf(
                'Argument #1 ($targetPath) must be of type string, %s given',
                gettype($targetPath),
            ));
        }

        $sourceStream = $this->getStream();

        $sourcePath = $sourceStream->getMetadata('uri');
        if (!is_string($sourcePath) || !is_file($sourcePath) || !is_readable($sourcePath)) {
            throw new RuntimeException('Uploaded file does not exist or is not readable');
        }

        $sourceDirname = dirname($sourcePath);
        if (!is_writable($sourceDirname)) {
            throw new RuntimeException('To move the uploaded file, the source directory must be writable');
        }

        $targetDirname = dirname($targetPath);
        if (!is_dir($targetDirname) || !is_writable($targetDirname)) {
            throw new RuntimeException('To move the uploaded file, the target directory must exist and be writable');
        }

        try {
            $this->isMoved = is_uploaded_file($sourcePath)
                ? move_uploaded_file($sourcePath, $targetPath)
                : rename($sourcePath, $targetPath);
        } catch (Throwable $e) {
        }

        if (!$this->isMoved) {
            throw new RuntimeException('Failed to move the uploaded file');
        }

        $sourceStream->close();
        $this->stream = null;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->errorCode;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
