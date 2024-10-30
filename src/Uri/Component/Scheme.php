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

use function is_string;
use function preg_match;
use function strtolower;

/**
 * @link https://tools.ietf.org/html/rfc3986#section-3.1
 */
final class Scheme implements ComponentInterface
{
    private const VALIDATION_REGEX = '/^(?:[A-Za-z][0-9A-Za-z\x2b\x2d\x2e]*)?$/';

    private string $value = '';

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value)
    {
        if ($value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('URI component "scheme" must be a string');
        }

        if (!preg_match(self::VALIDATION_REGEX, $value)) {
            throw new InvalidArgumentException('Invalid URI component "scheme"');
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
