<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import classes
 */
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Header\HeaderInterface;
use Sunrise\Stream\StreamFactory;
use InvalidArgumentException;

/**
 * Import functions
 */
use function is_string;
use function join;
use function preg_match;
use function sprintf;
use function strtolower;
use function ucwords;

/**
 * Hypertext Transfer Protocol Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Message implements MessageInterface
{

    /**
     * The message protocol version
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * The message headers
     *
     * @var array<string, array<string>>
     */
    protected $headers = [];

    /**
     * The message body
     *
     * @var StreamInterface|null
     */
    protected $body = null;

    /**
     * Constructor of the class
     *
     * @param array<string, string|array<string>>|null $headers
     * @param StreamInterface|null $body
     * @param string|null $protocolVersion
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ?array $headers = null,
        ?StreamInterface $body = null,
        ?string $protocolVersion = null
    ) {
        if (isset($protocolVersion)) {
            $this->setProtocolVersion($protocolVersion);
        }

        if (isset($headers)) {
            foreach ($headers as $name => $value) {
                $this->addHeader($name, $value);
            }
        }

        if (isset($body)) {
            $this->body = $body;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function withProtocolVersion($version) : MessageInterface
    {
        $clone = clone $this;
        $clone->setProtocolVersion($version);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name) : bool
    {
        $name = $this->normalizeHeaderName($name);

        return ! empty($this->headers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name) : array
    {
        $name = $this->normalizeHeaderName($name);

        return $this->headers[$name] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name) : string
    {
        $name = $this->normalizeHeaderName($name);

        if (empty($this->headers[$name])) {
            return '';
        }

        return join(', ', $this->headers[$name]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function withHeader($name, $value) : MessageInterface
    {
        $clone = clone $this;
        $clone->addHeader($name, $value);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function withAddedHeader($name, $value) : MessageInterface
    {
        $clone = clone $this;
        $clone->addHeader($name, $value, false);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name) : MessageInterface
    {
        $name = $this->normalizeHeaderName($name);

        $clone = clone $this;

        unset($clone->headers[$name]);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody() : StreamInterface
    {
        if (null === $this->body) {
            $this->body = (new StreamFactory)->createStream();
        }

        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body) : MessageInterface
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * Sets the given protocol version to the message
     *
     * @param string $version
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function setProtocolVersion($version) : void
    {
        $this->validateProtocolVersion($version);

        $this->protocolVersion = $version;
    }

    /**
     * Adds the given header field to the message
     *
     * @param string $name
     * @param string|array<string> $value
     * @param bool $replace
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function addHeader($name, $value, bool $replace = true) : void
    {
        $this->validateHeaderName($name);
        $this->validateHeaderValue($value);

        $name = $this->normalizeHeaderName($name);
        $value = (array) $value;

        if ($replace) {
            $this->headers[$name] = $value;
            return;
        }

        foreach ($value as $item) {
            $this->headers[$name][] = $item;
        }
    }

    /**
     * Validates the given protocol version
     *
     * @param mixed $version
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *
     * @link https://tools.ietf.org/html/rfc7230#section-2.6
     * @link https://tools.ietf.org/html/rfc7540
     */
    protected function validateProtocolVersion($version) : void
    {
        // allowed protocol versions:
        static $allowed = [
            '1.0' => 1,
            '1.1' => 1,
            '2.0' => 1,
            '2'   => 1,
        ];

        if (!is_string($version)) {
            throw new InvalidArgumentException('Protocol version must be a string');
        }

        if (!isset($allowed[$version])) {
            throw new InvalidArgumentException(sprintf(
                'The protocol version "%s" is not valid. ' .
                'Allowed only: 1.0, 1.1 or 2{.0}',
                $version
            ));
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
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     */
    protected function validateHeaderName($name) : void
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Header name must be a string');
        }

        if (!preg_match(HeaderInterface::RFC7230_TOKEN, $name)) {
            throw new InvalidArgumentException(sprintf('The header name "%s" is not valid', $name));
        }
    }

    /**
     * Validates the given header value
     *
     * @param mixed $value
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     */
    protected function validateHeaderValue($value) : void
    {
        $items = (array) $value;

        if ([] === $items) {
            throw new InvalidArgumentException('Header value must be a string or a non-empty array');
        }

        foreach ($items as $item) {
            if (!is_string($item)) {
                throw new InvalidArgumentException('Header value must be a string or an array with strings only');
            }

            if (!preg_match(HeaderInterface::RFC7230_FIELD_VALUE, $item)) {
                throw new InvalidArgumentException(sprintf('The header value "%s" is not valid', $item));
            }
        }
    }

    /**
     * Normalizes the given header name
     *
     * @param string $name
     *
     * @return string
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     */
    protected function normalizeHeaderName($name) : string
    {
        return ucwords(strtolower($name), '-');
    }
}
