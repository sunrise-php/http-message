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
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.15
 */
class ContentMD5Header extends Header
{

    /**
     * Regular Expression for a MD5 digest validation
     *
     * @link https://tools.ietf.org/html/rfc2045#section-6.8
     *
     * @var string
     */
    public const RFC2045_MD5_DIGEST = '/^[A-Za-z0-9\+\/]+=*$/';

    /**
     * @var string
     */
    private string $value;

    /**
     * Constructor of the class
     *
     * @param string $value
     *
     * @throws InvalidHeaderException
     *         If the value isn't valid.
     */
    public function __construct(string $value)
    {
        $this->validateValueByRegex(self::RFC2045_MD5_DIGEST, $value);

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-MD5';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return $this->value;
    }
}
