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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

use function array_key_exists;
use function array_walk_recursive;
use function is_array;
use function is_object;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var array<array-key, mixed>
     */
    private array $serverParams;

    /**
     * @var array<array-key, mixed>
     */
    private array $queryParams;

    /**
     * @var array<array-key, mixed>
     */
    private array $cookieParams;

    /**
     * @var array<array-key, mixed>
     */
    private array $uploadedFiles = [];

    /**
     * @var array<array-key, mixed>|object|null
     */
    private $parsedBody = null;

    /**
     * @var array<array-key, mixed>
     */
    private array $attributes;

    /**
     * Constructor of the class
     *
     * @param mixed $uri
     * @param array<string, string|string[]>|null $headers
     *
     * @param array<array-key, mixed> $serverParams
     * @param array<array-key, mixed> $queryParams
     * @param array<array-key, mixed> $cookieParams
     * @param array<array-key, mixed> $uploadedFiles
     * @param array<array-key, mixed>|object|null $parsedBody
     * @param array<array-key, mixed> $attributes
     *
     * @throws InvalidArgumentException
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
        parent::__construct($method, $uri, $headers, $body);

        if ($protocolVersion !== null) {
            $this->setProtocolVersion($protocolVersion);
        }

        if ($uploadedFiles !== []) {
            $this->setUploadedFiles($uploadedFiles);
        }

        if ($parsedBody !== null) {
            $this->setParsedBody($parsedBody);
        }

        $this->serverParams = $serverParams;
        $this->queryParams = $queryParams;
        $this->cookieParams = $cookieParams;
        $this->attributes = $attributes;
    }

    /**
     * {@inheritDoc}
     *
     * @return array<array-key, mixed>
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * {@inheritDoc}
     *
     * @return array<array-key, mixed>
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<array-key, mixed> $query
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
     * {@inheritDoc}
     *
     * @return array<array-key, mixed>
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<array-key, mixed> $cookies
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
     * {@inheritDoc}
     *
     * @return array<array-key, mixed>
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<array-key, mixed> $uploadedFiles
     *
     * @return static
     *
     * @throws InvalidArgumentException
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->setUploadedFiles($uploadedFiles);

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return array<array-key, mixed>|object|null
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<array-key, mixed>|object|null $data
     *
     * @return static
     *
     * @throws InvalidArgumentException
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->setParsedBody($data);

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return array<array-key, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * @param array<array-key, mixed> $files
     *
     * @throws InvalidArgumentException
     */
    final protected function setUploadedFiles(array $files): void
    {
        $this->validateUploadedFiles($files);

        $this->uploadedFiles = $files;
    }

    /**
     * Sets the given parsed body to the request
     *
     * @param array<array-key, mixed>|object|null $data
     *
     * @throws InvalidArgumentException
     */
    final protected function setParsedBody($data): void
    {
        $this->validateParsedBody($data);

        $this->parsedBody = $data;
    }

    /**
     * Validates the given uploaded files
     *
     * @param array<array-key, mixed> $files
     *
     * @throws InvalidArgumentException
     */
    private function validateUploadedFiles(array $files): void
    {
        if ($files === []) {
            return;
        }

        /**
         * @psalm-suppress MissingClosureParamType
         */
        array_walk_recursive($files, static function ($file): void {
            if (!($file instanceof UploadedFileInterface)) {
                throw new InvalidArgumentException('Invalid uploaded file');
            }
        });
    }

    /**
     * Validates the given parsed body
     *
     * @param mixed $data
     *
     * @throws InvalidArgumentException
     */
    private function validateParsedBody($data): void
    {
        if ($data === null || is_array($data) || is_object($data)) {
            return;
        }

        throw new InvalidArgumentException('Invalid parsed body');
    }
}
