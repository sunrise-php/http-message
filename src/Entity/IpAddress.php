<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Entity;

/**
 * Import functions
 */
use function filter_var;

/**
 * Import constants
 */
use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;
use const FILTER_VALIDATE_IP;

/**
 * IP address
 */
final class IpAddress
{

    /**
     * The IP address value
     *
     * @var string
     */
    private string $value;

    /**
     * The list of proxies in front of this IP address
     *
     * @var list<string>
     */
    private array $proxies;

    /**
     * Constructor of the class
     *
     * @param string $value
     * @param list<string> $proxies
     */
    public function __construct(string $value, array $proxies = [])
    {
        $this->value = $value;
        $this->proxies = $proxies;
    }

    /**
     * Gets the IP address value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Gets the list of proxies in front of this IP address
     *
     * @return list<string>
     */
    public function getProxies(): array
    {
        return $this->proxies;
    }

    /**
     * Checks if the IP address is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return false !== filter_var($this->value, FILTER_VALIDATE_IP);
    }

    /**
     * Checks if the IP address is IPv4
     *
     * @return bool
     */
    public function isVersion4(): bool
    {
        return false !== filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Checks if the IP address is IPv6
     *
     * @return bool
     */
    public function isVersion6(): bool
    {
        return false !== filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Checks if the IP address is in the private range
     *
     * @return bool
     */
    public function isInPrivateRange(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        return false === filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
    }

    /**
     * Checks if the IP address is in the reserved range
     *
     * @return bool
     */
    public function isInReservedRange(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        return false === filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);
    }
}
