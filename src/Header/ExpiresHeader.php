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
use Sunrise\Http\Message\HeaderUtils;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.21
 * @link https://tools.ietf.org/html/rfc822#section-5
 */
class ExpiresHeader extends Header
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
        return 'Expires';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return HeaderUtils::formatDate($this->timestamp);
    }
}
