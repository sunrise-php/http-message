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
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Exception\RuntimeException;
use Throwable;

/**
 * Import functions
 */
use function fclose;
use function feof;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function is_resource;
use function stream_get_contents;
use function stream_get_meta_data;
use function strpbrk;

/**
 * Import constants
 */
use const SEEK_SET;

/**
 * Stream
 *
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Stream implements StreamInterface
{

    /**
     * The stream resource
     *
     * @var resource|null
     */
    private $resource;

    /**
     * Signals to close the stream on destruction
     *
     * @var bool
     */
    private $autoClose;

    /**
     * Constructor of the class
     *
     * @param mixed $resource
     * @param bool $autoClose
     *
     * @throws InvalidArgumentException
     *         If the stream cannot be initialized with the resource.
     */
    public function __construct($resource, bool $autoClose = true)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Unexpected stream resource');
        }

        $this->resource = $resource;
        $this->autoClose = $autoClose;
    }

    /**
     * Creates a stream
     *
     * @param mixed $resource
     *
     * @return StreamInterface
     *
     * @throws InvalidArgumentException
     *         If the stream cannot be initialized with the resource.
     */
    public static function create($resource): StreamInterface
    {
        if ($resource instanceof StreamInterface) {
            return $resource;
        }

        return new self($resource);
    }

    /**
     * Destructor of the class
     */
    public function __destruct()
    {
        if ($this->autoClose) {
            $this->close();
        }
    }

    /**
     * Detaches a resource from the stream
     *
     * Returns NULL if the stream already without a resource.
     *
     * @return resource|null
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    /**
     * Closes the stream
     *
     * @link http://php.net/manual/en/function.fclose.php
     *
     * @return void
     */
    public function close(): void
    {
        $resource = $this->detach();
        if (!is_resource($resource)) {
            return;
        }

        fclose($resource);
    }

    /**
     * Checks if the end of the stream is reached
     *
     * @link http://php.net/manual/en/function.feof.php
     *
     * @return bool
     */
    public function eof(): bool
    {
        if (!is_resource($this->resource)) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * Gets the stream pointer position
     *
     * @link http://php.net/manual/en/function.ftell.php
     *
     * @return int
     *
     * @throws RuntimeException
     */
    public function tell(): int
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('The stream does not have a resource, so the operation is not possible');
        }

        $result = ftell($this->resource);
        if ($result === false) {
            throw new RuntimeException('Unable to get the stream pointer position');
        }

        return $result;
    }

    /**
     * Checks if the stream is seekable
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        if (!is_resource($this->resource)) {
            return false;
        }

        /** @var array{seekable: bool} */
        $metadata = stream_get_meta_data($this->resource);

        return $metadata['seekable'];
    }

    /**
     * Moves the stream pointer to the beginning
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * Moves the stream pointer to the given position
     *
     * @link http://php.net/manual/en/function.fseek.php
     *
     * @param int $offset
     * @param int $whence
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('The stream does not have a resource, so the operation is not possible');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        $result = fseek($this->resource, $offset, $whence);
        if ($result !== 0) {
            throw new RuntimeException('Unable to move the stream pointer position');
        }
    }

    /**
     * Checks if the stream is writable
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        if (!is_resource($this->resource)) {
            return false;
        }

        /** @var array{mode: string} */
        $metadata = stream_get_meta_data($this->resource);

        return strpbrk($metadata['mode'], '+acwx') !== false;
    }

    /**
     * Writes the given string to the stream
     *
     * Returns the number of bytes written to the stream.
     *
     * @link http://php.net/manual/en/function.fwrite.php
     *
     * @param string $string
     *
     * @return int
     *
     * @throws RuntimeException
     */
    public function write($string): int
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('The stream does not have a resource, so the operation is not possible');
        }

        if (!$this->isWritable()) {
            throw new RuntimeException('Stream is not writable');
        }

        $result = fwrite($this->resource, $string);
        if ($result === false) {
            throw new RuntimeException('Unable to write to the stream');
        }

        return $result;
    }

    /**
     * Checks if the stream is readable
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        if (!is_resource($this->resource)) {
            return false;
        }

        /** @var array{mode: string} */
        $metadata = stream_get_meta_data($this->resource);

        return strpbrk($metadata['mode'], '+r') !== false;
    }

    /**
     * Reads the given number of bytes from the stream
     *
     * @link http://php.net/manual/en/function.fread.php
     *
     * @param int $length
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function read($length): string
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('The stream does not have a resource, so the operation is not possible');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $result = fread($this->resource, $length);
        if ($result === false) {
            throw new RuntimeException('Unable to read from the stream');
        }

        return $result;
    }

    /**
     * Reads the remainder of the stream
     *
     * @link http://php.net/manual/en/function.stream-get-contents.php
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getContents(): string
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('The stream does not have a resource, so the operation is not possible');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $result = stream_get_contents($this->resource);
        if ($result === false) {
            throw new RuntimeException('Unable to read the remainder of the stream');
        }

        return $result;
    }

    /**
     * Gets the stream metadata
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if (!is_resource($this->resource)) {
            return null;
        }

        $metadata = stream_get_meta_data($this->resource);
        if ($key === null) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }

    /**
     * Gets the stream size
     *
     * Returns NULL if the stream doesn't have a resource,
     * or if the stream size cannot be determined.
     *
     * @link http://php.net/manual/en/function.fstat.php
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        if (!is_resource($this->resource)) {
            return null;
        }

        /** @var array{size: int}|false */
        $stats = fstat($this->resource);
        if ($stats === false) {
            return null;
        }

        return $stats['size'];
    }

    /**
     * Converts the stream to a string
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#object.tostring
     *
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->isReadable()) {
            return '';
        }

        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (Throwable $e) {
            return '';
        }
    }
}
