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
use Fig\Http\Message\RequestMethodInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Header\HeaderInterface;
use Sunrise\Uri\UriFactory;

/**
 * Import functions
 */
use function is_string;
use function preg_match;
use function sprintf;
use function strncmp;
use function strtoupper;

/**
 * HTTP Request Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Request extends Message implements RequestInterface, RequestMethodInterface
{

    /**
     * The request method (aka verb)
     *
     * @var string
     */
    protected $method = self::METHOD_GET;

    /**
     * The request target
     *
     * @var string|null
     */
    protected $requestTarget = null;

    /**
     * The request URI
     *
     * @var UriInterface|null
     */
    protected $uri = null;

    /**
     * Constructor of the class
     *
     * @param string|null $method
     * @param string|UriInterface|null $uri
     * @param array<string, string|string[]>|null $headers
     * @param StreamInterface|null $body
     * @param string|null $requestTarget
     * @param string|null $protocolVersion
     */
    public function __construct(
        ?string $method = null,
        $uri = null,
        ?array $headers = null,
        ?StreamInterface $body = null,
        ?string $requestTarget = null,
        ?string $protocolVersion = null
    ) {
        parent::__construct(
            $headers,
            $body,
            $protocolVersion
        );

        if (isset($method)) {
            $this->setMethod($method);
        }

        if (isset($requestTarget)) {
            $this->setRequestTarget($requestTarget);
        }

        if (isset($uri)) {
            $this->setUri($uri);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method) : RequestInterface
    {
        $clone = clone $this;
        $clone->setMethod($method);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget() : string
    {
        if (isset($this->requestTarget)) {
            return $this->requestTarget;
        }

        $uri = $this->getUri();
        $path = $uri->getPath();

        // https://tools.ietf.org/html/rfc7230#section-5.3.1
        // https://tools.ietf.org/html/rfc7230#section-2.7
        //
        // origin-form = absolute-path [ "?" query ]
        // absolute-path = 1*( "/" segment )
        if (0 !== strncmp($path, '/', 1)) {
            return '/';
        }

        $requestTarget = $path;

        $query = $uri->getQuery();
        if ('' !== $query) {
            $requestTarget .= '?' . $query;
        }

        return $requestTarget;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget) : RequestInterface
    {
        $clone = clone $this;
        $clone->setRequestTarget($requestTarget);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri() : UriInterface
    {
        if (null === $this->uri) {
            $this->uri = (new UriFactory)->createUri();
        }

        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false) : RequestInterface
    {
        $clone = clone $this;
        $clone->setUri($uri, $preserveHost);

        return $clone;
    }

    /**
     * Sets the given method to the request
     *
     * @param string $method
     *
     * @return void
     */
    protected function setMethod($method) : void
    {
        $this->validateMethod($method);

        $this->method = strtoupper($method);
    }

    /**
     * Sets the given request-target to the request
     *
     * @param mixed $requestTarget
     *
     * @return void
     */
    protected function setRequestTarget($requestTarget) : void
    {
        $this->validateRequestTarget($requestTarget);

        /**
         * @var string $requestTarget
         */

        $this->requestTarget = $requestTarget;
    }

    /**
     * Sets the given URI to the request
     *
     * @param string|UriInterface $uri
     * @param bool $preserveHost
     *
     * @return void
     */
    protected function setUri($uri, $preserveHost = false) : void
    {
        if (! ($uri instanceof UriInterface)) {
            $uri = (new UriFactory)->createUri($uri);
        }

        $this->uri = $uri;

        if ('' === $uri->getHost() || ($preserveHost && $this->hasHeader('Host'))) {
            return;
        }

        $newHost = $uri->getHost();

        $port = $uri->getPort();
        if (null !== $port) {
            $newHost .= ':' . $port;
        }

        $this->addHeader('Host', $newHost);
    }

    /**
     * Validates the given method
     *
     * @param mixed $method
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.1.1
     */
    protected function validateMethod($method) : void
    {
        if (!is_string($method)) {
            throw new InvalidArgumentException('HTTP method must be a string');
        }

        if (!preg_match(HeaderInterface::RFC7230_TOKEN, $method)) {
            throw new InvalidArgumentException(sprintf('HTTP method "%s" is not valid', $method));
        }
    }

    /**
     * Validates the given request-target
     *
     * @param mixed $requestTarget
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *
     * @link https://tools.ietf.org/html/rfc7230#section-5.3
     */
    protected function validateRequestTarget($requestTarget) : void
    {
        if (!is_string($requestTarget)) {
            throw new InvalidArgumentException('HTTP request-target must be a string');
        }

        if (!preg_match('/^[\x21-\x7E\x80-\xFF]+$/', $requestTarget)) {
            throw new InvalidArgumentException(sprintf('HTTP request-target "%s" is not valid', $requestTarget));
        }
    }
}
