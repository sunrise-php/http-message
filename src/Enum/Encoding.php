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
 * Encoding
 */
final class Encoding
{
    public const BR = 'br';
    public const CHUNKED = 'chunked';
    public const COMPRESS = 'compress';
    public const DEFLATE = 'deflate';
    public const GZIP = 'gzip';
}
