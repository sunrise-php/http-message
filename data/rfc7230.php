<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Regular Expression for a token validation
 *
 * @var string
 */
const RFC7230_TOKEN = '/^[\x21\x23-\x27\x2A\x2B\x2D\x2E\x30-\x39\x41-\x5A\x5E-\x7A\x7C\x7E]+$/';

/**
 * Regular Expression for a field-value validation
 *
 * @var string
 */
const RFC7230_FIELD_VALUE = '/^[\x09\x20-\x7E\x80-\xFF]*$/';
