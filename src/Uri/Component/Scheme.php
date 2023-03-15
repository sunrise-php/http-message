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
use function is_string;
use function preg_match;
use function strtolower;

/**
 * URI component "scheme"
 *
 * @link https://tools.ietf.org/html/rfc3986#section-3.1
 */
final class Scheme implements ComponentInterface
{

    /**
     * Regular expression used for the component validation
     *
     * @var string
     */
    private const VALIDATION_REGEX = '/^(?:[A-Za-z][0-9A-Za-z\x2b\x2d\x2e]*)?$/';

    /**
     * The component value
     *
     * @var string
     */
    private string $value = '';

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
        if ($value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidUriComponentException('URI component "scheme" must be a string');
        }

        if (!preg_match(self::VALIDATION_REGEX, $value)) {
            throw new InvalidUriComponentException('Invalid URI component "scheme"');
        }

        // the component is case-insensitive...
        $this->value = strtolower($value);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
