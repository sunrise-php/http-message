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

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Message\Stream\PhpInputStream;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @param array<array-key, mixed>|null $serverParams
     * @param array<array-key, mixed>|null $queryParams
     * @param array<array-key, mixed>|null $cookieParams
     * @param array<array-key, mixed>|null $uploadedFiles
     * @param array<array-key, mixed>|null $parsedBody
     *
     * @link http://php.net/manual/en/language.variables.superglobals.php
     * @link https://www.php-fig.org/psr/psr-15/meta/
     */
    public static function fromGlobals(
        ?array $serverParams = null,
        ?array $queryParams = null,
        ?array $cookieParams = null,
        ?array $uploadedFiles = null,
        ?array $parsedBody = null
    ): ServerRequestInterface {
        $serverParams ??= $_SERVER;
        $queryParams ??= $_GET;
        $cookieParams ??= $_COOKIE;
        $uploadedFiles ??= $_FILES;
        $parsedBody ??= $_POST;

        return new ServerRequest(
            server_request_protocol_version($serverParams),
            server_request_method($serverParams),
            server_request_uri($serverParams),
            server_request_headers($serverParams),
            new PhpInputStream(),
            $serverParams,
            $queryParams,
            $cookieParams,
            server_request_files($uploadedFiles),
            $parsedBody
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $uri
     * @param array<array-key, mixed> $serverParams
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest(
            server_request_protocol_version($serverParams),
            $method,
            $uri,
            server_request_headers($serverParams),
            null, // body
            $serverParams
        );
    }
}
