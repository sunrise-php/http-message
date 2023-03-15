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
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

/**
 * Import functions
 */
use function is_string;
use function preg_match;
use function strncmp;

/**
 * HTTP Request Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Request extends Message implements RequestInterface, RequestMethodInterface
{

    /**
     * Regular Expression used for a request target validation
     *
     * @var string
     */
    public const RFC7230_REQUEST_TARGET_REGEX = '/^[\x21-\x7E\x80-\xFF]+$/';

    /**
     * The request method (aka verb)
     *
     * @var string
     */
    private string $method = self::METHOD_GET;

    /**
     * The request URI
     *
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * The request target
     *
     * @var string|null
     */
    private ?string $requestTarget = null;

    /**
     * Constructor of the class
     *
     * @param string|null $method
     * @param mixed $uri
     * @param array<string, string|string[]>|null $headers
     * @param StreamInterface|null $body
     *
     * @throws InvalidArgumentException
     *         If one of the arguments isn't valid.
     */
    public function __construct(
        ?string $method = null,
        $uri = null,
        ?array $headers = null,
        ?StreamInterface $body = null
    ) {
        if (isset($method)) {
            $this->setMethod($method);
        }

        $this->setUri($uri ?? '/');

        if (isset($headers)) {
            $this->setHeaders($headers);
        }

        if (isset($body)) {
            $this->setBody($body);
        }
    }

    /**
     * Gets the request method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Creates a new instance of the request with the given method
     *
     * @param string $method
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the method isn't valid.
     */
    public function withMethod($method): RequestInterface
    {
        $clone = clone $this;
        $clone->setMethod($method);

        return $clone;
    }

    /**
     * Gets the request URI
     *
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Creates a new instance of the request with the given URI
     *
     * @param UriInterface $uri
     * @param bool $preserveHost
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        $clone = clone $this;
        $clone->setUri($uri, $preserveHost);

        return $clone;
    }

    /**
     * Gets the request target
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        if (isset($this->requestTarget)) {
            return $this->requestTarget;
        }

        $requestTarget = $this->uri->getPath();

        // https://tools.ietf.org/html/rfc7230#section-5.3.1
        // https://tools.ietf.org/html/rfc7230#section-2.7
        //
        // origin-form = absolute-path [ "?" query ]
        // absolute-path = 1*( "/" segment )
        if (strncmp($requestTarget, '/', 1) !== 0) {
            return '/';
        }

        $queryString = $this->uri->getQuery();
        if ($queryString !== '') {
            $requestTarget .= '?' . $queryString;
        }

        return $requestTarget;
    }

    /**
     * Creates a new instance of the request with the given request target
     *
     * @param mixed $requestTarget
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the request target isn't valid.
     */
    public function withRequestTarget($requestTarget): RequestInterface
    {
        $clone = clone $this;
        $clone->setRequestTarget($requestTarget);

        return $clone;
    }

    /**
     * Sets the given method to the request
     *
     * @param string $method
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the method isn't valid.
     */
    final protected function setMethod($method): void
    {
        $this->validateMethod($method);

        $this->method = $method;
    }

    /**
     * Sets the given URI to the request
     *
     * @param mixed $uri
     * @param bool $preserveHost
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the URI isn't valid.
     */
    final protected function setUri($uri, $preserveHost = false): void
    {
        $this->uri = Uri::create($uri);

        if ($preserveHost && $this->hasHeader('Host')) {
            return;
        }

        $host = $this->uri->getHost();
        if ($host === '') {
            return;
        }

        $port = $this->uri->getPort();
        if (isset($port)) {
            $host .= ':' . $port;
        }

        $this->setHeader('Host', $host, true);
    }

    /**
     * Sets the given request target to the request
     *
     * @param mixed $requestTarget
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the request target isn't valid.
     */
    final protected function setRequestTarget($requestTarget): void
    {
        $this->validateRequestTarget($requestTarget);

        /** @var string $requestTarget */

        $this->requestTarget = $requestTarget;
    }

    /**
     * Validates the given method
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.1.1
     *
     * @param mixed $method
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the method isn't valid.
     */
    private function validateMethod($method): void
    {
        if ('' === $method) {
            throw new InvalidArgumentException('HTTP method cannot be an empty');
        }

        if (!is_string($method)) {
            throw new InvalidArgumentException('HTTP method must be a string');
        }

        if (!preg_match(HeaderInterface::RFC7230_TOKEN_REGEX, $method)) {
            throw new InvalidArgumentException('Invalid HTTP method');
        }
    }

    /**
     * Validates the given request target
     *
     * @param mixed $requestTarget
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the request target isn't valid.
     */
    private function validateRequestTarget($requestTarget): void
    {
        if ('' === $requestTarget) {
            throw new InvalidArgumentException('HTTP request target cannot be an empty');
        }

        if (!is_string($requestTarget)) {
            throw new InvalidArgumentException('HTTP request target must be a string');
        }

        if (!preg_match(self::RFC7230_REQUEST_TARGET_REGEX, $requestTarget)) {
            throw new InvalidArgumentException('Invalid HTTP request target');
        }
    }
}
