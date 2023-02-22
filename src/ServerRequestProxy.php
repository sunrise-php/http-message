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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Entity\IpAddress;

/**
 * Import functions
 */
use function explode;
use function key;
use function reset;
use function strncmp;
use function strpos;
use function strstr;
use function strtolower;
use function trim;

/**
 * ServerRequestProxy
 */
final class ServerRequestProxy implements ServerRequestInterface, RequestMethodInterface
{

    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    /**
     * Constructor of the class
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Creates the proxy from the given object
     *
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function create(ServerRequestInterface $request): self
    {
        if ($request instanceof self) {
            return $request;
        }

        return new self($request);
    }

    /**
     * Checks if the request is JSON
     *
     * @link https://tools.ietf.org/html/rfc4627
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->clientProducesMediaType([
            'application/json',
        ]);
    }

    /**
     * Checks if the request is XML
     *
     * @link https://tools.ietf.org/html/rfc2376
     *
     * @return bool
     */
    public function isXml(): bool
    {
        return $this->clientProducesMediaType([
            'application/xml',
            'text/xml',
        ]);
    }

    /**
     * Gets the client's IP address
     *
     * @param array<string, string> $proxyChain
     *
     * @return IpAddress
     */
    public function getClientIpAddress(array $proxyChain = []): IpAddress
    {
        $env = $this->request->getServerParams();

        /** @var string */
        $client = $env['REMOTE_ADDR'] ?? '::1';

        /** @var list<string> */
        $proxies = [];

        while (isset($proxyChain[$client])) {
            $proxyHeader = $proxyChain[$client];
            unset($proxyChain[$client]);

            $header = $this->request->getHeaderLine($proxyHeader);
            if ($header === '') {
                break;
            }

            $addresses = explode(',', $header);
            foreach ($addresses as $i => $address) {
                $addresses[$i] = trim($address);
                if ($addresses[$i] === '') {
                    unset($addresses[$i]);
                }
            }

            if ($addresses === []) {
                break;
            }

            $client = reset($addresses);
            unset($addresses[key($addresses)]);

            foreach ($addresses as $address) {
                $proxies[] = $address;
            }
        }

        return new IpAddress($client, $proxies);
    }

    /**
     * Gets the client's produced media type
     *
     * @link https://tools.ietf.org/html/rfc7231#section-3.1.1.1
     * @link https://tools.ietf.org/html/rfc7231#section-3.1.1.5
     *
     * @return string
     */
    public function getClientProducedMediaType(): string
    {
        $header = $this->request->getHeaderLine('Content-Type');
        if ($header === '') {
            return '';
        }

        $result = strstr($header, ';', true);
        if ($result === false) {
            $result = $header;
        }

        $result = trim($result);
        if ($result === '') {
            return '';
        }

        return strtolower($result);
    }

    /**
     * Gets the client's consumed media types
     *
     * @link https://tools.ietf.org/html/rfc7231#section-1.2
     * @link https://tools.ietf.org/html/rfc7231#section-3.1.1.1
     * @link https://tools.ietf.org/html/rfc7231#section-5.3.2
     *
     * @return array<string, array<string, string>>
     */
    public function getClientConsumedMediaTypes(): array
    {
        $header = $this->request->getHeaderLine('Accept');
        if ($header === '') {
            return [];
        }

        $accept = header_accept_parse($header);
        if ($accept === []) {
            return [];
        }

        $result = [];
        foreach ($accept as $type => $params) {
            $result[strtolower($type)] = $params;
        }

        return $result;
    }

    /**
     * Gets the client's consumed encodings
     *
     * @return array<string, array<string, string>>
     */
    public function getClientConsumedEncodings(): array
    {
        $header = $this->request->getHeaderLine('Accept-Encoding');
        if ($header === '') {
            return [];
        }

        $accept = header_accept_parse($header);
        if ($accept === []) {
            return [];
        }

        return $accept;
    }

    /**
     * Gets the client's consumed languages
     *
     * @return array<string, array<string, string>>
     */
    public function getClientConsumedLanguages(): array
    {
        $header = $this->request->getHeaderLine('Accept-Language');
        if ($header === '') {
            return [];
        }

        $accept = header_accept_parse($header);
        if ($accept === []) {
            return [];
        }

        return $accept;
    }

    /**
     * Checks if the client produces one of the given media types
     *
     * @param list<string> $consumes
     *
     * @return bool
     */
    public function clientProducesMediaType(array $consumes): bool
    {
        if ($consumes === []) {
            return true;
        }

        $produced = $this->getClientProducedMediaType();
        if ($produced === '') {
            return false;
        }

        foreach ($consumes as $consumed) {
            if ($this->equalsMediaTypes($consumed, $produced)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the client consumes one of the given media types
     *
     * @param list<string> $produces
     *
     * @return bool
     */
    public function clientConsumesMediaType(array $produces): bool
    {
        if ($produces === []) {
            return true;
        }

        $consumes = $this->getClientConsumedMediaTypes();
        if ($consumes === []) {
            return true;
        }

        if (isset($consumes['*/*'])) {
            return true;
        }

        foreach ($produces as $a) {
            foreach ($consumes as $b => $_) {
                if ($this->equalsMediaTypes($a, $b)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks if the given media types are equal
     *
     * @param string $a
     * @param string $b
     *
     * @return bool
     */
    public function equalsMediaTypes(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }

        $slash = strpos($a, '/');
        if ($slash === false || !isset($b[$slash]) || $b[$slash] !== '/') {
            return false;
        }

        $star = $slash + 1;
        if (!isset($a[$star], $b[$star])) {
            return false;
        }

        if (!($a[$star] === '*' || $b[$star] === '*')) {
            return false;
        }

        return strncmp($a, $b, $star) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(): string
    {
        return $this->request->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withProtocolVersion($version);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->request->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name): bool
    {
        return $this->request->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name): array
    {
        return $this->request->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name): string
    {
        return $this->request->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withHeader($name, $value);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withAddedHeader($name, $value);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withoutHeader($name);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(): StreamInterface
    {
        return $this->request->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withBody($body);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withMethod($method);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): UriInterface
    {
        return $this->request->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withUri($uri, $preserveHost);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget(): string
    {
        return $this->request->getRequestTarget();
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withRequestTarget($requestTarget);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams(): array
    {
        return $this->request->getServerParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams(): array
    {
        return $this->request->getQueryParams();
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withQueryParams($query);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams(): array
    {
        return $this->request->getCookieParams();
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withCookieParams($cookies);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles(): array
    {
        return $this->request->getUploadedFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withUploadedFiles($uploadedFiles);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->request->getParsedBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withParsedBody($data);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->request->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        return $this->request->getAttribute($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withAttribute($name, $value);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $clone = clone $this;
        $clone->request = $clone->request->withoutAttribute($name);

        return $clone;
    }
}
