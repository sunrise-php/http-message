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
 * HTTP authentication schemes
 *
 * @link https://www.iana.org/assignments/http-authschemes/http-authschemes.xhtml
 */
final class AuthenticationScheme
{
    public const BASIC         = 'Basic';
    public const BEARER        = 'Bearer';
    public const DIGEST        = 'Digest';
    public const HOBA          = 'HOBA';
    public const MUTUAL        = 'Mutual';
    public const NEGOTIATE     = 'Negotiate';
    public const OAUTH         = 'OAuth';
    public const SCRAM_SHA_1   = 'SCRAM-SHA-1';
    public const SCRAM_SHA_256 = 'SCRAM-SHA-256';
    public const VAPID         = 'vapid';
}
