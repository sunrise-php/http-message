<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Uri\Component;

use Sunrise\Http\Message\Exception\InvalidArgumentException;

use function is_int;

/**
 * @link https://tools.ietf.org/html/rfc3986#section-3.2.3
 */
final class Port implements ComponentInterface
{
    private const MIN_VALUE = 1;
    private const MAX_VALUE = (2 ** 16) - 1;

    private ?int $value = null;

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value)
    {
        if ($value === null) {
            return;
        }

        if (!is_int($value)) {
            throw new InvalidArgumentException('URI component "port" must be an integer');
        }

        if (!($value >= self::MIN_VALUE && $value <= self::MAX_VALUE)) {
            throw new InvalidArgumentException('Invalid URI component "port"');
        }

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }
}
