<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Enum;

/**
 * HTTP warning codes
 *
 * @link https://www.iana.org/assignments/http-warn-codes/http-warn-codes.xhtml
 */
final class WarningCode
{
    public const RESPONSE_IS_STALE = 110;
    public const REVALIDATION_FAILED = 111;
    public const DISCONNECTED_OPERATION = 112;
    public const HEURISTIC_EXPIRATION = 113;
    public const MISCELLANEOUS_WARNING = 199;
    public const TRANSFORMATION_APPLIED = 214;
    public const MISCELLANEOUS_PERSISTENT_WARNING = 299;
}
