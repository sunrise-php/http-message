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
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Uri\Component\Fragment;
use Sunrise\Http\Message\Uri\Component\Host;
use Sunrise\Http\Message\Uri\Component\Path;
use Sunrise\Http\Message\Uri\Component\Port;
use Sunrise\Http\Message\Uri\Component\Query;
use Sunrise\Http\Message\Uri\Component\Scheme;
use Sunrise\Http\Message\Uri\Component\UserInfo;

/**
 * Import functions
 */
use function is_string;
use function ltrim;
use function parse_url;
use function strncmp;

/**
 * Uniform Resource Identifier
 *
 * @link https://tools.ietf.org/html/rfc3986
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Uri implements UriInterface
{

    /**
     * Scheme of the URI
     *
     * @var string
     */
    private string $scheme = '';

    /**
     * User Information of the URI
     *
     * @var string
     */
    private string $userInfo = '';

    /**
     * Host of the URI
     *
     * @var string
     */
    private string $host = '';

    /**
     * Port of the URI
     *
     * @var int|null
     */
    private ?int $port = null;

    /**
     * Path of the URI
     *
     * @var string
     */
    private string $path = '';

    /**
     * Query of the URI
     *
     * @var string
     */
    private string $query = '';

    /**
     * Fragment of the URI
     *
     * @var string
     */
    private string $fragment = '';

    /**
     * Constructor of the class
     *
     * @param string $uri
     *
     * @throws InvalidArgumentException
     *         If the URI isn't valid.
     */
    public function __construct(string $uri = '')
    {
        if ($uri === '') {
            return;
        }

        $components = parse_url($uri);
        if ($components === false) {
            throw new InvalidArgumentException('Unable to parse URI');
        }

        if (isset($components['scheme'])) {
            $this->setScheme($components['scheme']);
        }

        if (isset($components['user'])) {
            $this->setUserInfo(
                $components['user'],
                $components['pass'] ?? null
            );
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

    /**
     * Creates a URI
     *
     * @param mixed $uri
     *
     * @return UriInterface
     *
     * @throws InvalidArgumentException
     *         If the URI isn't valid.
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
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the scheme isn't valid.
     */
    public function withScheme($scheme): UriInterface
    {
        $clone = clone $this;
        $clone->setScheme($scheme);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the user information isn't valid.
     */
    public function withUserInfo($user, $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->setUserInfo($user, $password);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the host isn't valid.
     */
    public function withHost($host): UriInterface
    {
        $clone = clone $this;
        $clone->setHost($host);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the port isn't valid.
     */
    public function withPort($port): UriInterface
    {
        $clone = clone $this;
        $clone->setPort($port);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the path isn't valid.
     */
    public function withPath($path): UriInterface
    {
        $clone = clone $this;
        $clone->setPath($path);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the query isn't valid.
     */
    public function withQuery($query): UriInterface
    {
        $clone = clone $this;
        $clone->setQuery($query);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *         If the fragment isn't valid.
     */
    public function withFragment($fragment): UriInterface
    {
        $clone = clone $this;
        $clone->setFragment($fragment);

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
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
            $authority = $authority . ':' . $port;
        }

        return $authority;
    }

    /**
     * {@inheritdoc}
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
     * Sets the given scheme to the URI
     *
     * @param mixed $scheme
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the scheme isn't valid.
     */
    final protected function setScheme($scheme): void
    {
        $this->scheme = (new Scheme($scheme))->getValue();
    }

    /**
     * Sets the given user information to the URI
     *
     * @param mixed $user
     * @param mixed $password
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the user information isn't valid.
     */
    final protected function setUserInfo($user, $password): void
    {
        $this->userInfo = (new UserInfo($user, $password))->getValue();
    }

    /**
     * Sets the given host to the URI
     *
     * @param mixed $host
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the host isn't valid.
     */
    final protected function setHost($host): void
    {
        $this->host = (new Host($host))->getValue();
    }

    /**
     * Sets the given port to the URI
     *
     * @param mixed $port
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the port isn't valid.
     */
    final protected function setPort($port): void
    {
        $this->port = (new Port($port))->getValue();
    }

    /**
     * Sets the given path to the URI
     *
     * @param mixed $path
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the path isn't valid.
     */
    final protected function setPath($path): void
    {
        $this->path = (new Path($path))->getValue();
    }

    /**
     * Sets the given query to the URI
     *
     * @param mixed $query
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the query isn't valid.
     */
    final protected function setQuery($query): void
    {
        $this->query = (new Query($query))->getValue();
    }

    /**
     * Sets the given fragment to the URI
     *
     * @param mixed $fragment
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the fragment isn't valid.
     */
    final protected function setFragment($fragment): void
    {
        $this->fragment = (new Fragment($fragment))->getValue();
    }
}
