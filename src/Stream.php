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
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Exception\RuntimeException;
use Throwable;

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

use const SEEK_SET;

class Stream implements StreamInterface
{
    /**
     * @var resource|null
     */
    private $resource;

    private bool $autoClose;

    /**
     * @param mixed $resource
     *
     * @throws InvalidArgumentException
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
     * @param mixed $resource
     *
     * @throws InvalidArgumentException
     */
    public static function create($resource): StreamInterface
    {
        if ($resource instanceof StreamInterface) {
            return $resource;
        }

        return new self($resource);
    }

    public function __destruct()
    {
        if ($this->autoClose) {
            $this->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function eof(): bool
    {
        if (!is_resource($this->resource)) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Stream has no resource');
        }

        $result = ftell($this->resource);
        if ($result === false) {
            throw new RuntimeException('Unable to get the stream pointer position');
        }

        return $result;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Stream has no resource');
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function write($string): int
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Stream has no resource');
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
     * @inheritDoc
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
     * @inheritDoc
     *
     * @psalm-param int $length
     * @phpstan-param int<1, max> $length
     */
    public function read($length): string
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Stream has no resource');
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
     * @inheritDoc
     */
    public function getContents(): string
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Stream has no resource');
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
