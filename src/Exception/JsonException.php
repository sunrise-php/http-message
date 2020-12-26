<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Exception;

/**
 * Import classes
 */
use RuntimeException;

/**
 * Import functions
 */
use function json_last_error;
use function json_last_error_msg;

/**
 * Import constants
 */
use const JSON_ERROR_NONE;

/**
 * JsonException
 */
class JsonException extends RuntimeException
{

    /**
     * @return void
     *
     * @throws self
     */
    public static function assert() : void
    {
        $code = json_last_error();

        if (JSON_ERROR_NONE === $code) {
            return;
        }

        throw new self(json_last_error_msg(), $code);
    }
}
