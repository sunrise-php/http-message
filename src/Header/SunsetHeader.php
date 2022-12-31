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
use DateTimeInterface;
use Sunrise\Http\Message\Header;

/**
 * @link https://tools.ietf.org/id/draft-wilde-sunset-header-03.html
 * @link https://github.com/sunrise-php/http-header-kit/issues/1#issuecomment-457043527
 */
class SunsetHeader extends Header
{

    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $timestamp;

    /**
     * Constructor of the class
     *
     * @param DateTimeInterface $timestamp
     */
    public function __construct(DateTimeInterface $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Sunset';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return $this->formatDateTime($this->timestamp);
    }
}
