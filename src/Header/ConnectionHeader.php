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
use Sunrise\Http\Message\Exception\InvalidHeaderValueException;
use Sunrise\Http\Message\Header;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.10
 */
class ConnectionHeader extends Header
{

    /**
     * @var string
     */
    public const CONNECTION_CLOSE = 'close';

    /**
     * @var string
     */
    public const CONNECTION_KEEP_ALIVE = 'keep-alive';

    /**
     * @var string
     */
    private string $value;

    /**
     * Constructor of the class
     *
     * @param string $value
     *
     * @throws InvalidHeaderValueException
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
        return 'Connection';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return $this->value;
    }
}
