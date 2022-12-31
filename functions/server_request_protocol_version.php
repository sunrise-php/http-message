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
 * Import functions
 */
use function sprintf;
use function sscanf;

/**
 * Gets the request protocol version
 *
 * @param array|null $serverParams
 *
 * @return string
 *
 * @link http://php.net/manual/en/reserved.variables.server.php
 * @link https://datatracker.ietf.org/doc/html/rfc3875#section-4.1.16
 */
function server_request_protocol_version(?array $serverParams = null): string
{
    $serverParams ??= $_SERVER;

    if (!isset($serverParams['SERVER_PROTOCOL'])) {
        return '1.1';
    }

    // "HTTP" "/" 1*digit "." 1*digit
    sscanf($serverParams['SERVER_PROTOCOL'], 'HTTP/%d.%d', $major, $minor);

    // e.g.: HTTP/1.1
    if (isset($minor)) {
        return sprintf('%d.%d', $major, $minor);
    }

    // e.g.: HTTP/2
    if (isset($major)) {
        return sprintf('%d', $major);
    }

    return '1.1';
}
