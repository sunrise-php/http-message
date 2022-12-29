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
 * @link https://tools.ietf.org/html/rfc2616#section-14.19
 */
class EtagHeader extends Header
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
     * @throws InvalidHeaderValueException
     *         If the value isn't valid.
     */
    public function __construct(string $value)
    {
        $this->validateQuotedString($value);

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'ETag';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return sprintf('"%s"', $this->value);
    }
}
