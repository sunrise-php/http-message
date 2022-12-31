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
 * @link https://tools.ietf.org/html/rfc2616#section-14.13
 */
class ContentLengthHeader extends Header
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
        return 'Content-Length';
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
        if (! ($value >= 0)) {
            throw new InvalidHeaderValueException(sprintf(
                'The value "%2$d" for the header "%1$s" is not valid',
                $this->getFieldName(),
                $value
            ));
        }
    }
}
