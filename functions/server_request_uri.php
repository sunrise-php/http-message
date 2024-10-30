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

use function array_key_exists;

function server_request_uri(?array $serverParams = null): UriInterface
{
    $serverParams ??= $_SERVER;

    if (array_key_exists('HTTPS', $serverParams)) {
        if ('off' !== $serverParams['HTTPS']) {
            $scheme = 'https://';
        }
    }

    if (array_key_exists('HTTP_HOST', $serverParams)) {
        $host = $serverParams['HTTP_HOST'];
    } elseif (array_key_exists('SERVER_NAME', $serverParams)) {
        $host = $serverParams['SERVER_NAME'];
        if (array_key_exists('SERVER_PORT', $serverParams)) {
            $host .= ':' . $serverParams['SERVER_PORT'];
        }
    }

    if (array_key_exists('REQUEST_URI', $serverParams)) {
        $target = $serverParams['REQUEST_URI'];
    } elseif (array_key_exists('PHP_SELF', $serverParams)) {
        $target = $serverParams['PHP_SELF'];
        if (array_key_exists('QUERY_STRING', $serverParams)) {
            $target .= '?' . $serverParams['QUERY_STRING'];
        }
    }

    return new Uri(
        ($scheme ?? 'http://') .
        ($host ?? 'localhost') .
        ($target ?? '/')
    );
}
