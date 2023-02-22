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
 * Import functions
 */
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.16
 */
class ContentRangeHeader extends Header
{

    /**
     * @var int
     */
    private int $firstBytePosition;

    /**
     * @var int
     */
    private int $lastBytePosition;

    /**
     * @var int
     */
    private int $instanceLength;

    /**
     * Constructor of the class
     *
     * @param int $firstBytePosition
     * @param int $lastBytePosition
     * @param int $instanceLength
     *
     * @throws InvalidHeaderException
     *         If the range isn't valid.
     */
    public function __construct(int $firstBytePosition, int $lastBytePosition, int $instanceLength)
    {
        $this->validateRange($firstBytePosition, $lastBytePosition, $instanceLength);

        $this->firstBytePosition = $firstBytePosition;
        $this->lastBytePosition = $lastBytePosition;
        $this->instanceLength = $instanceLength;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-Range';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return sprintf(
            'bytes %d-%d/%d',
            $this->firstBytePosition,
            $this->lastBytePosition,
            $this->instanceLength
        );
    }

    /**
     * Validates the given range
     *
     * @param int $firstBytePosition
     * @param int $lastBytePosition
     * @param int $instanceLength
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If the range isn't valid.
     */
    private function validateRange(int $firstBytePosition, int $lastBytePosition, int $instanceLength): void
    {
        if (! ($firstBytePosition <= $lastBytePosition)) {
            throw new InvalidHeaderException(
                'The "first-byte-pos" value of the content range ' .
                'must be less than or equal to the "last-byte-pos" value'
            );
        }

        if (! ($lastBytePosition < $instanceLength)) {
            throw new InvalidHeaderException(
                'The "last-byte-pos" value of the content range ' .
                'must be less than the "instance-length" value'
            );
        }
    }
}
