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
 * @extends IteratorAggregate<int, string>
 */
interface HeaderInterface extends IteratorAggregate
{

    /**
     * Date format according to RFC-822
     *
     * @var string
     */
    public const RFC822_DATE_FORMAT = 'D, d M y H:i:s O';

    /**
     * Regular Expression used for a token validation according to RFC-7230
     *
     * @var string
     */
    public const RFC7230_TOKEN_REGEX = '/^[\x21\x23-\x27\x2A\x2B\x2D\x2E\x30-\x39\x41-\x5A\x5E-\x7A\x7C\x7E]+$/';

    /**
     * Regular Expression used for a field-value validation according to RFC-7230
     *
     * @var string
     */
    public const RFC7230_FIELD_VALUE_REGEX = '/^[\x09\x20-\x7E\x80-\xFF]*$/';

    /**
     * Regular Expression used for a quoted-string validation according to RFC-7230
     *
     * @var string
     */
    public const RFC7230_QUOTED_STRING_REGEX = '/^(?:[\x5C][\x22]|[\x09\x20\x21\x23-\x5B\x5D-\x7E\x80-\xFF])*$/';

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
     * Converts the header to a field
     *
     * @return string
     */
    public function __toString(): string;
}
