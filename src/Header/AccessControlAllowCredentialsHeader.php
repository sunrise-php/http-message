<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Header;

/**
 * Import classes
 */
use Sunrise\Http\Message\Header;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Credentials
 */
class AccessControlAllowCredentialsHeader extends Header
{

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Access-Control-Allow-Credentials';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return 'true';
    }
}
