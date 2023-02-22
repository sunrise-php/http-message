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
 * Import classes
 */
use IteratorAggregate;

/**
 * <code>
 *   $response->withHeader(...new SetCookieHeader('foo', 'bar'))
 * </code>
 *
 * @extends IteratorAggregate<int, string>
 */
interface HeaderInterface extends IteratorAggregate
{

    /**
     * Gets the header field name
     *
     * @return string
     */
    public function getFieldName(): string;

    /**
     * Gets the header field value
     *
     * @return string
     */
    public function getFieldValue(): string;

    /**
     * Converts the header field to a string
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#object.tostring
     *
     * @return string
     */
    public function __toString(): string;
}
