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
 * CookieSameSite
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
 */
final class CookieSameSite
{

    /**
     * Cookies are not sent on normal cross-site subrequests, but are
     * sent when a user is navigating to the origin site.
     *
     * This is the default cookie value if SameSite has not been
     * explicitly specified in recent browser versions.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite#lax
     */
    public const LAX = 'Lax';

    /**
     * Cookies will only be sent in a first-party context and not be
     * sent along with requests initiated by third party websites.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite#strict
     */
    public const STRICT = 'Strict';

    /**
     * Cookies will be sent in all contexts, i.e. in responses to both
     * first-party and cross-site requests.
     *
     * If SameSite=None is set, the cookie Secure attribute must also
     * be set (or the cookie will be blocked).
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite#none
     */
    public const NONE = 'None';
}
