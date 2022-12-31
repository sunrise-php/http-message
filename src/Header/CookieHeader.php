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
 * Import functions
 */
use function http_build_query;

/**
 * Import constants
 */
use const PHP_QUERY_RFC3986;

/**
 * @link https://tools.ietf.org/html/rfc6265.html#section-5.4
 */
class CookieHeader extends Header
{

    /**
     * @var array
     */
    private array $value;

    /**
     * Constructor of the class
     *
     * @param array $value
     */
    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Cookie';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return http_build_query($this->value, '', '; ', PHP_QUERY_RFC3986);
    }
}
