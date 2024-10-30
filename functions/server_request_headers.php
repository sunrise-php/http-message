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

use function strncmp;
use function strtolower;
use function strtr;
use function substr;
use function ucwords;

/**
 * @return array<string, string>
 *
 * @link http://php.net/manual/en/reserved.variables.server.php
 * @link https://datatracker.ietf.org/doc/html/rfc3875#section-4.1.18
 */
function server_request_headers(?array $serverParams = null): array
{
    $serverParams ??= $_SERVER;

    // https://datatracker.ietf.org/doc/html/rfc3875#section-4.1.2
    if (!isset($serverParams['HTTP_CONTENT_LENGTH']) && isset($serverParams['CONTENT_LENGTH'])) {
        $serverParams['HTTP_CONTENT_LENGTH'] = $serverParams['CONTENT_LENGTH'];
    }

    // https://datatracker.ietf.org/doc/html/rfc3875#section-4.1.3
    if (!isset($serverParams['HTTP_CONTENT_TYPE']) && isset($serverParams['CONTENT_TYPE'])) {
        $serverParams['HTTP_CONTENT_TYPE'] = $serverParams['CONTENT_TYPE'];
    }

    $result = [];
    foreach ($serverParams as $key => $value) {
        if (strncmp('HTTP_', $key, 5) !== 0) {
            continue;
        }

        $name = strtr(substr($key, 5), '_', '-');
        $name = ucwords(strtolower($name), '-');

        $result[$name] = $value;
    }

    return $result;
}
