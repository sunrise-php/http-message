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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Stream\PhpTempStream;

use function implode;
use function is_array;
use function is_string;
use function preg_match;
use function strtolower;

abstract class Message implements MessageInterface
{
    /**
     * @deprecated 3.2.0
     */
    public const ALLOWED_HTTP_VERSIONS = ['1.0', '1.1', '2.0', '2'];

    public const HTTP_VERSION_REGEX = '/^[0-9](?:[.][0-9])?$/';
    public const DEFAULT_HTTP_VERSION = '1.1';

    private string $protocolVersion = self::DEFAULT_HTTP_VERSION;

    /**
     * @var array<string, list<string>>
     */
    private array $headers = [];

    /**
     * @var array<string, string>
     */
    private array $headerNames = [];

    private ?StreamInterface $body = null;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function withProtocolVersion($version): MessageInterface
    {
        $clone = clone $this;
        $clone->setProtocolVersion($version);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name): bool
    {
        $key = strtolower($name);

        return isset($this->headerNames[$key]);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name): array
    {
        $key = strtolower($name);

        if (!isset($this->headerNames[$key])) {
            return [];
        }

        return $this->headers[$this->headerNames[$key]];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        $key = strtolower($name);

        if (!isset($this->headerNames[$key])) {
            return '';
        }

        return implode(',', $this->headers[$this->headerNames[$key]]);
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value): MessageInterface
    {
        $clone = clone $this;
        $clone->setHeader($name, $value, true);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value): MessageInterface
    {
        $clone = clone $this;
        $clone->setHeader($name, $value, false);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name): MessageInterface
    {
        $clone = clone $this;
        $clone->deleteHeader($name);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body ??= new PhpTempStream();
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $clone = clone $this;
        $clone->setBody($body);

        return $clone;
    }

    /**
     * Sets the given HTTP version to the message
     *
     * @param string $protocolVersion
     *
     * @throws InvalidArgumentException
     */
    final protected function setProtocolVersion($protocolVersion): void
    {
        $this->validateProtocolVersion($protocolVersion);

        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Sets a new header to the message with the given name and value(s)
     *
     * @param string $name
     * @param string|string[] $value
     *
     * @throws InvalidArgumentException
     */
    final protected function setHeader($name, $value, bool $replace = true): void
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->validateHeaderName($name);
        $this->validateHeaderValue($name, $value);

        $replace and $this->deleteHeader($name);

        $key = strtolower($name);

        $this->headerNames[$key] ??= $name;
        $this->headers[$this->headerNames[$key]] ??= [];

        foreach ($value as $item) {
            $this->headers[$this->headerNames[$key]][] = $item;
        }
    }

    /**
     * Sets the given headers to the message
     *
     * @param array<string, string|string[]> $headers
     *
     * @throws InvalidArgumentException
     */
    final protected function setHeaders(array $headers): void
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value, false);
        }
    }

    /**
     * Deletes a header from the message by the given name
     *
     * @param string $name
     */
    final protected function deleteHeader($name): void
    {
        $key = strtolower($name);

        if (isset($this->headerNames[$key])) {
            unset($this->headers[$this->headerNames[$key]]);
            unset($this->headerNames[$key]);
        }
    }

    /**
     * Sets the given body to the message
     */
    final protected function setBody(StreamInterface $body): void
    {
        $this->body = $body;
    }

    /**
     * Validates the given HTTP version
     *
     * @param mixed $protocolVersion
     *
     * @throws InvalidArgumentException
     */
    private function validateProtocolVersion($protocolVersion): void
    {
        if ($protocolVersion === '') {
            throw new InvalidArgumentException('HTTP version cannot be an empty');
        }

        if (!is_string($protocolVersion)) {
            throw new InvalidArgumentException('HTTP version must be a string');
        }

        if (!preg_match(self::HTTP_VERSION_REGEX, $protocolVersion)) {
            throw new InvalidArgumentException('HTTP version is invalid');
        }
    }

    /**
     * Validates the given header name
     *
     * @param mixed $name
     *
     * @throws InvalidArgumentException
     */
    private function validateHeaderName($name): void
    {
        if ($name === '') {
            throw new InvalidArgumentException('HTTP header name cannot be an empty');
        }

        if (!is_string($name)) {
            throw new InvalidArgumentException('HTTP header name must be a string');
        }

        if (!preg_match(HeaderInterface::RFC7230_TOKEN_REGEX, $name)) {
            throw new InvalidArgumentException('HTTP header name is invalid');
        }
    }

    /**
     * Validates the given header value
     *
     * @param array<array-key, mixed> $value
     *
     * @throws InvalidArgumentException
     */
    private function validateHeaderValue(string $name, array $value): void
    {
        if ($value === []) {
            throw new InvalidArgumentException("The value of the HTTP header {$name} cannot be an empty array");
        }

        foreach ($value as $key => $item) {
            if ($item === '') {
                continue;
            }

            if (!is_string($item)) {
                throw new InvalidArgumentException("The value of the HTTP header {$name}[{$key}] must be a string");
            }

            if (!preg_match(HeaderInterface::RFC7230_FIELD_VALUE_REGEX, $item)) {
                throw new InvalidArgumentException("The value of the HTTP header {$name}[{$key}] is invalid");
            }
        }
    }
}
