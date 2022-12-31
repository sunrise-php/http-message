<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Stream;

/**
 * Import classes
 */
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Stream;

/**
 * Import functions
 */
use function fopen;
use function sprintf;

/**
 * @link https://www.php.net/manual/en/wrappers.php.php#wrappers.php.memory
 */
class PhpTempStream extends Stream
{

    /**
     * Constructor of the class
     *
     * @param string $mode
     * @param int $maxmemory
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $mode = 'r+b', int $maxmemory = 2097152)
    {
        if ($maxmemory < 0) {
            throw new InvalidArgumentException('Argument #2 ($maxmemory) must be greater than or equal to 0');
        }

        parent::__construct(fopen(sprintf('php://temp/maxmemory:%d', $maxmemory), $mode));
    }
}
