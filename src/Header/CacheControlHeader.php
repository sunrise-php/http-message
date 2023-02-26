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
use function implode;
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.9
 */
class CacheControlHeader extends Header
{

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * Constructor of the class
     *
     * @param array<array-key, mixed> $parameters
     *
     * @throws InvalidHeaderException
     *         If the parameters aren't valid.
     */
    public function __construct(array $parameters)
    {
        $parameters = $this->validateParameters($parameters);

        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Cache-Control';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $segments = [];
        foreach ($this->parameters as $name => $value) {
            // e.g., no-cache
            if ($value === '') {
                $segments[] = $name;
                continue;
            }

            // e.g., max-age=604800
            if ($this->isToken($value)) {
                $segments[] = sprintf('%s=%s', $name, $value);
                continue;
            }

            // e.g., community="UCI"
            $segments[] = sprintf('%s="%s"', $name, $value);
        }

        return implode(', ', $segments);
    }
}
