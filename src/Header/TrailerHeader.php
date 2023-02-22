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
 * @link https://tools.ietf.org/html/rfc2616#section-14.40
 */
class TrailerHeader extends Header
{

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
        $this->validateToken($value);

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Trailer';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return $this->value;
    }
}
