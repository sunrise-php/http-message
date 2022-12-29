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
 * InvalidStreamException
 */
class InvalidStreamException extends RuntimeException
{

    /**
     * @return self
     */
    final public static function noResource(): self
    {
        return new self('The stream without a resource so the operation is not possible');
    }
}
