<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Exception;

/**
 * Import classes
 */
use RuntimeException as BaseRuntimeException;

/**
 * RuntimeException
 */
class RuntimeException extends BaseRuntimeException implements ExceptionInterface
{
}
