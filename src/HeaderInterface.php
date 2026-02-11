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

interface HeaderInterface
{
    public const RFC822_DATE_FORMAT = 'D, d M y H:i:s O';

    public const RFC7230_TOKEN_REGEX = '/^[\x21\x23-\x27\x2A\x2B\x2D\x2E\x30-\x39\x41-\x5A\x5E-\x7A\x7C\x7E]+$/';

    public const RFC7230_FIELD_VALUE_REGEX = '/^[\x09\x20-\x7E\x80-\xFF]*$/';

    public const RFC7230_QUOTED_STRING_REGEX = '/^(?:[\x5C][\x22]|[\x09\x20\x21\x23-\x5B\x5D-\x7E\x80-\xFF])*$/';

    public function getFieldName(): string;

    public function getFieldValue(): string;

    public function __toString(): string;
}
