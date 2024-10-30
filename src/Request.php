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

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

use function is_string;
use function preg_match;
use function strncmp;

class Request extends Message implements RequestInterface, RequestMethodInterface
{
    private const RFC7230_REQUEST_TARGET_REGEX = '/^[\x21-\x7E\x80-\xFF]+$/';

    private string $method = self::METHOD_GET;
    private UriInterface $uri;
    private ?string $requestTarget = null;

    /**
     * @param mixed $uri
     * @param array<string, string|string[]>|null $headers
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ?string $method = null,
        $uri = null,
        ?array $headers = null,
        ?StreamInterface $body = null
    ) {
        if ($method !== null) {
            $this->setMethod($method);
        }

        $this->setUri($uri ?? '/');

        if ($headers !== null) {
            $this->setHeaders($headers);
        }

        if ($body !== null) {
            $this->setBody($body);
        }
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method): RequestInterface
    {
        $clone = clone $this;
        $clone->setMethod($method);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        $clone = clone $this;
        $clone->setUri($uri, $preserveHost);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
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
     * @inheritDoc
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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
        if ($port !== null) {
            $host .= ':' . $port;
        }

        $this->setHeader('Host', $host, true);
    }

    /**
     * Sets the given request target to the request
     *
     * @param mixed $requestTarget
     *
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    private function validateMethod($method): void
    {
        if ($method === '') {
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
     * @throws InvalidArgumentException
     */
    private function validateRequestTarget($requestTarget): void
    {
        if ($requestTarget === '') {
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
