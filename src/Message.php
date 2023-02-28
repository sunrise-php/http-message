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
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Stream\PhpTempStream;

/**
 * Import functions
 */
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function preg_match;
use function sprintf;
use function strtolower;

/**
 * Hypertext Transfer Protocol Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
abstract class Message implements MessageInterface
{

    /**
     * Supported HTTP versions
     *
     * @var list<string>
     */
    public const SUPPORTED_HTTP_VERSIONS = ['1.0', '1.1', '2.0', '2'];

    /**
     * Default HTTP version
     *
     * @var string
     */
    public const DEFAULT_HTTP_VERSION = '1.1';

    /**
     * The message HTTP version
     *
     * @var string
     */
    private string $protocolVersion = self::DEFAULT_HTTP_VERSION;

    /**
     * The message headers
     *
     * @var array<string, list<string>>
     */
    private array $headers = [];

    /**
     * Original header names (see $headers)
     *
     * @var array<string, string>
     */
    private array $headerNames = [];

    /**
     * The message body
     *
     * @var StreamInterface|null
     */
    private ?StreamInterface $body = null;

    /**
     * Gets the message HTTP version
     *
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Creates a new instance of the message with the given HTTP version
     *
     * @param string $version
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the HTTP version isn't valid.
     */
    public function withProtocolVersion($version): MessageInterface
    {
        $clone = clone $this;
        $clone->setProtocolVersion($version);

        return $clone;
    }

    /**
     * Gets the message headers
     *
     * @return array<string, list<string>>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists in the message by the given name
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name): bool
    {
        $key = strtolower($name);

        return isset($this->headerNames[$key]);
    }

    /**
     * Gets a header value from the message by the given name
     *
     * @param string $name
     *
     * @return list<string>
     */
    public function getHeader($name): array
    {
        $key = strtolower($name);

        if (empty($this->headerNames[$key])) {
            return [];
        }

        return $this->headers[$this->headerNames[$key]];
    }

    /**
     * Gets a header value as a string from the message by the given name
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine($name): string
    {
        $value = $this->getHeader($name);
        if ([] === $value) {
            return '';
        }

        return implode(',', $value);
    }

    /**
     * Creates a new instance of the message with the given header overwriting the old header
     *
     * @param string $name
     * @param string|string[] $value
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the header isn't valid.
     */
    public function withHeader($name, $value): MessageInterface
    {
        $clone = clone $this;
        $clone->setHeader($name, $value, true);

        return $clone;
    }

    /**
     * Creates a new instance of the message with the given header NOT overwriting the old header
     *
     * @param string $name
     * @param string|string[] $value
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the header isn't valid.
     */
    public function withAddedHeader($name, $value): MessageInterface
    {
        $clone = clone $this;
        $clone->setHeader($name, $value, false);

        return $clone;
    }

    /**
     * Creates a new instance of the message without a header by the given name
     *
     * @param string $name
     *
     * @return static
     */
    public function withoutHeader($name): MessageInterface
    {
        $clone = clone $this;
        $clone->deleteHeader($name);

        return $clone;
    }

    /**
     * Gets the message body
     *
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body ??= new PhpTempStream();
    }

    /**
     * Creates a new instance of the message with the given body
     *
     * @param StreamInterface $body
     *
     * @return static
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
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the HTTP version isn't valid.
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
     * @param bool $replace
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the header isn't valid.
     */
    final protected function setHeader($name, $value, bool $replace = true): void
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->validateHeaderName($name);
        $this->validateHeaderValue($name, $value);

        if ($replace) {
            $this->deleteHeader($name);
        }

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
     * @return void
     *
     * @throws InvalidArgumentException
     *         If one of the headers isn't valid.
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
     *
     * @return void
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
     *
     * @param StreamInterface $body
     *
     * @return void
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
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the HTTP version isn't valid.
     */
    private function validateProtocolVersion($protocolVersion): void
    {
        if (!in_array($protocolVersion, self::SUPPORTED_HTTP_VERSIONS, true)) {
            throw new InvalidArgumentException('Invalid or unsupported HTTP version');
        }
    }

    /**
     * Validates the given header name
     *
     * @param mixed $name
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the header name isn't valid.
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
     * @param string $name
     * @param array $value
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the header value isn't valid.
     */
    private function validateHeaderValue(string $name, array $value): void
    {
        if ([] === $value) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" HTTP header value cannot be an empty array',
                $name,
            ));
        }

        foreach ($value as $key => $item) {
            if ('' === $item) {
                continue;
            }

            if (!is_string($item)) {
                throw new InvalidArgumentException(sprintf(
                    'The "%s[%s]" HTTP header value must be a string',
                    $name,
                    $key
                ));
            }

            if (!preg_match(HeaderInterface::RFC7230_FIELD_VALUE_REGEX, $item)) {
                throw new InvalidArgumentException(sprintf(
                    'The "%s[%s]" HTTP header value is invalid',
                    $name,
                    $key
                ));
            }
        }
    }
}
