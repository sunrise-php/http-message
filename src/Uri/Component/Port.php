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

/**
 * Import classes
 */
use Sunrise\Http\Message\Exception\InvalidUriComponentException;

/**
 * Import functions
 */
use function is_int;

/**
 * URI component "port"
 *
 * @link https://tools.ietf.org/html/rfc3986#section-3.2.3
 */
final class Port implements ComponentInterface
{

    /**
     * The component value
     *
     * @var int|null
     */
    private ?int $value = null;

    /**
     * Constructor of the class
     *
     * @param mixed $value
     *
     * @throws InvalidUriComponentException
     *         If the component isn't valid.
     */
    public function __construct($value)
    {
        $min = 1;
        $max = (2 ** 16) - 1;

        if ($value === null) {
            return;
        }

        if (!is_int($value)) {
            throw new InvalidUriComponentException('URI component "port" must be an integer');
        }

        if (!($value >= $min && $value <= $max)) {
            throw new InvalidUriComponentException('Invalid URI component "port"');
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
