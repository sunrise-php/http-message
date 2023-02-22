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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

/**
 * Import functions
 */
use function array_key_exists;
use function array_walk_recursive;
use function is_array;
use function is_object;

/**
 * ServerRequest
 *
 * @link https://www.php-fig.org/psr/psr-7/
 */
class ServerRequest extends Request implements ServerRequestInterface
{

    /**
     * The server parameters
     *
     * @var array
     */
    private array $serverParams;

    /**
     * The request's query parameters
     *
     * @var array
     */
    private array $queryParams;

    /**
     * The request's cookie parameters
     *
     * @var array
     */
    private array $cookieParams;

    /**
     * The request's uploaded files
     *
     * @var array
     */
    private array $uploadedFiles;

    /**
     * The request's parsed body
     *
     * @var array|object|null
     */
    private $parsedBody;

    /**
     * The request attributes
     *
     * @var array
     */
    private array $attributes;

    /**
     * Constructor of the class
     *
     * @param string|null $protocolVersion
     * @param string|null $method
     * @param mixed $uri
     * @param array<string, string|string[]>|null $headers
     * @param StreamInterface|null $body
     *
     * @param array $serverParams
     * @param array $queryParams
     * @param array $cookieParams
     * @param array $uploadedFiles
     * @param array|object|null $parsedBody
     * @param array $attributes
     *
     * @throws InvalidArgumentException
     *         If one of the arguments isn't valid.
     */
    public function __construct(
        ?string $protocolVersion = null,
        ?string $method = null,
        $uri = null,
        ?array $headers = null,
        ?StreamInterface $body = null,
        array $serverParams = [],
        array $queryParams = [],
        array $cookieParams = [],
        array $uploadedFiles = [],
        $parsedBody = null,
        array $attributes = []
    ) {
        if (isset($protocolVersion)) {
            $this->setProtocolVersion($protocolVersion);
        }

        parent::__construct($method, $uri, $headers, $body);

        $this->serverParams = $serverParams;
        $this->queryParams = $queryParams;
        $this->cookieParams = $cookieParams;
        $this->setUploadedFiles($uploadedFiles);
        $this->setParsedBody($parsedBody);
        $this->attributes = $attributes;
    }

    /**
     * Gets the server parameters
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * Gets the request's query parameters
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Creates a new instance of the request with the given query parameters
     *
     * @param array $query
     *
     * @return static
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * Gets the request's cookie parameters
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Creates a new instance of the request with the given cookie parameters
     *
     * @param array $cookies
     *
     * @return static
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * Gets the request's uploaded files
     *
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * Creates a new instance of the request with the given uploaded files
     *
     * @param array $uploadedFiles
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If one of the files isn't valid.
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->setUploadedFiles($uploadedFiles);

        return $clone;
    }

    /**
     * Gets the request's parsed body
     *
     * @return array|object|null
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * Creates a new instance of the request with the given parsed body
     *
     * @param array|object|null $data
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the data isn't valid.
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->setParsedBody($data);

        return $clone;
    }

    /**
     * Gets the request attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Gets the request's attribute value by the given name
     *
     * Returns the default value if the attribute wasn't found.
     *
     * @param array-key $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * Creates a new instance of the request with the given attribute
     *
     * @param array-key $name
     * @param mixed $value
     *
     * @return static
     */
    public function withAttribute($name, $value): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * Creates a new instance of the request without an attribute with the given name
     *
     * @param array-key $name
     *
     * @return static
     */
    public function withoutAttribute($name): ServerRequestInterface
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }

    /**
     * Sets the given uploaded files to the request
     *
     * @param array $files
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If one of the files isn't valid.
     */
    final protected function setUploadedFiles(array $files): void
    {
        $this->validateUploadedFiles($files);

        $this->uploadedFiles = $files;
    }

    /**
     * Sets the given parsed body to the request
     *
     * @param array|object|null $data
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the parsed body isn't valid.
     */
    final protected function setParsedBody($data): void
    {
        $this->validateParsedBody($data);

        $this->parsedBody = $data;
    }

    /**
     * Validates the given uploaded files
     *
     * @param array $files
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If one of the files isn't valid.
     */
    private function validateUploadedFiles(array $files): void
    {
        if ([] === $files) {
            return;
        }

        /**
         * @param mixed $file
         *
         * @return void
         *
         * @throws InvalidArgumentException
         *
         * @psalm-suppress MissingClosureParamType
         */
        array_walk_recursive($files, static function ($file): void {
            if (! ($file instanceof UploadedFileInterface)) {
                throw new InvalidArgumentException('Invalid uploaded files');
            }
        });
    }

    /**
     * Validates the given parsed body
     *
     * @param mixed $data
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the parsed body isn't valid.
     */
    private function validateParsedBody($data): void
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new InvalidArgumentException('Invalid parsed body');
        }
    }
}
