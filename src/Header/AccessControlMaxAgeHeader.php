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
 * Import functions
 */
use function sprintf;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Max-Age
 */
class AccessControlMaxAgeHeader extends Header
{

    /**
     * @var int
     */
    private int $value;

    /**
     * Constructor of the class
     *
     * @param int $value
     *
     * @throws InvalidHeaderValueException
     *         If the value isn't valid.
     */
    public function __construct(int $value)
    {
        $this->validateValue($value);

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Access-Control-Max-Age';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return sprintf('%d', $this->value);
    }

    /**
     * Validates the given value
     *
     * @param int $value
     *
     * @return void
     *
     * @throws InvalidHeaderValueException
     *         If the value isn't valid.
     */
    private function validateValue(int $value): void
    {
        if (! ($value === -1 || $value >= 1)) {
            throw new InvalidHeaderValueException(sprintf(
                'The value "%2$d" for the header "%1$s" is not valid.',
                $this->getFieldName(),
                $value
            ));
        }
    }
}
