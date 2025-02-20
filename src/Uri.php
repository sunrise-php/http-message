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

use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Uri\Component\Fragment;
use Sunrise\Http\Message\Uri\Component\Host;
use Sunrise\Http\Message\Uri\Component\Path;
use Sunrise\Http\Message\Uri\Component\Port;
use Sunrise\Http\Message\Uri\Component\Query;
use Sunrise\Http\Message\Uri\Component\Scheme;
use Sunrise\Http\Message\Uri\Component\UserInfo;

use function is_string;
use function ltrim;
use function parse_url;
use function strncmp;

class Uri implements UriInterface
{
    private string $scheme = '';
    private string $userInfo = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $uri = '')
    {
        if ($uri === '') {
            return;
        }

        $this->parseUri($uri);
    }

    /**
     * @param mixed $uri
     *
     * @throws InvalidArgumentException
     */
    public static function create($uri): UriInterface
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        }

        if (!is_string($uri)) {
            throw new InvalidArgumentException('URI should be a string');
        }

        return new self($uri);
    }

    /**
     * @inheritDoc
     */
    public function withScheme($scheme): UriInterface
    {
        $clone = clone $this;
        $clone->setScheme($scheme);

        return $clone;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function withUserInfo($user, $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->setUserInfo($user, $password);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withHost($host): UriInterface
    {
        $clone = clone $this;
        $clone->setHost($host);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): UriInterface
    {
        $clone = clone $this;
        $clone->setPort($port);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): UriInterface
    {
        $clone = clone $this;
        $clone->setPath($path);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query): UriInterface
    {
        $clone = clone $this;
        $clone->setQuery($query);

        return $clone;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function withFragment($fragment): UriInterface
    {
        $clone = clone $this;
        $clone->setFragment($fragment);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        // The 80 is the default port number for the HTTP protocol.
        if ($this->port === 80 && $this->scheme === 'http') {
            return null;
        }

        // The 443 is the default port number for the HTTPS protocol.
        if ($this->port === 443 && $this->scheme === 'https') {
            return null;
        }

        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        // CVE-2015-3257
        if (strncmp($this->path, '//', 2) === 0) {
            return '/' . ltrim($this->path, '/');
        }

        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        // The host is the basic subcomponent.
        if ($this->host === '') {
            return '';
        }

        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        $port = $this->getPort();
        if ($port !== null) {
            $authority = $authority . ':' . (string) $port;
        }

        return $authority;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $uri = '';

        $scheme = $this->scheme;
        if ($scheme !== '') {
            $uri .= $scheme . ':';
        }

        $authority = $this->getAuthority();
        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        $path = $this->path;
        if ($path !== '') {
            // https://github.com/sunrise-php/uri/issues/31
            // https://datatracker.ietf.org/doc/html/rfc3986#section-3.3
            //
            // If a URI contains an authority component,
            // then the path component must either be empty
            // or begin with a slash ("/") character.
            if ($authority !== '' && strncmp($path, '/', 1) !== 0) {
                $path = '/' . $path;
            }

            // https://github.com/sunrise-php/uri/issues/31
            // https://datatracker.ietf.org/doc/html/rfc3986#section-3.3
            //
            // If a URI does not contain an authority component,
            // then the path cannot begin with two slash characters ("//").
            if ($authority === '' && strncmp($path, '//', 2) === 0) {
                $path = '/' . ltrim($path, '/');
            }

            $uri .= $path;
        }

        $query = $this->query;
        if ($query !== '') {
            $uri .= '?' . $query;
        }

        $fragment = $this->fragment;
        if ($fragment !== '') {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * @param mixed $scheme
     *
     * @throws InvalidArgumentException
     */
    final protected function setScheme($scheme): void
    {
        $this->scheme = (new Scheme($scheme))->getValue();
    }

    /**
     * @param mixed $user
     * @param mixed $password
     *
     * @throws InvalidArgumentException
     */
    final protected function setUserInfo($user, $password): void
    {
        $this->userInfo = (new UserInfo($user, $password))->getValue();
    }

    /**
     * @param mixed $host
     *
     * @throws InvalidArgumentException
     */
    final protected function setHost($host): void
    {
        $this->host = (new Host($host))->getValue();
    }

    /**
     * @param mixed $port
     *
     * @throws InvalidArgumentException
     */
    final protected function setPort($port): void
    {
        $this->port = (new Port($port))->getValue();
    }

    /**
     * @param mixed $path
     *
     * @throws InvalidArgumentException
     */
    final protected function setPath($path): void
    {
        $this->path = (new Path($path))->getValue();
    }

    /**
     * @param mixed $query
     *
     * @throws InvalidArgumentException
     */
    final protected function setQuery($query): void
    {
        $this->query = (new Query($query))->getValue();
    }

    /**
     * @param mixed $fragment
     *
     * @throws InvalidArgumentException
     */
    final protected function setFragment($fragment): void
    {
        $this->fragment = (new Fragment($fragment))->getValue();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function parseUri(string $uri): void
    {
        $components = parse_url($uri);
        if ($components === false) {
            throw new InvalidArgumentException('Invalid URI');
        }

        if (isset($components['scheme'])) {
            $this->setScheme($components['scheme']);
        }
        if (isset($components['user'])) {
            $this->setUserInfo($components['user'], $components['pass'] ?? null);
        }
        if (isset($components['host'])) {
            $this->setHost($components['host']);
        }
        if (isset($components['port'])) {
            $this->setPort($components['port']);
        }
        if (isset($components['path'])) {
            $this->setPath($components['path']);
        }
        if (isset($components['query'])) {
            $this->setQuery($components['query']);
        }
        if (isset($components['fragment'])) {
            $this->setFragment($components['fragment']);
        }
    }
}
