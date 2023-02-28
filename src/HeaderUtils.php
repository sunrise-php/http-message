<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import classes
 */
use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * HeaderUtils
 */
final class HeaderUtils
{

    /**
     * Formats the given date according to RFC-822
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    public static function formatDate(DateTimeInterface $date): string
    {
        if ($date instanceof DateTime) {
            $data = clone $date;
        }

        return $date->setTimezone(new DateTimeZone('GMT'))
            ->format(HeaderInterface::RFC822_DATE_FORMAT);
    }
}
