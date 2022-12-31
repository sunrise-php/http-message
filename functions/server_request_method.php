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
 * Gets the request method
 *
 * @param array|null $serverParams
 *
 * @return string
 *
 * @link http://php.net/manual/en/reserved.variables.server.php
 * @link https://datatracker.ietf.org/doc/html/rfc3875#section-4.1.12
 */
function server_request_method(?array $serverParams = null): string
{
    $serverParams ??= $_SERVER;

    return $serverParams['REQUEST_METHOD'] ?? Request::METHOD_GET;
}
