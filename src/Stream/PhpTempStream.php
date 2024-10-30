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

use Sunrise\Http\Message\Stream;

use function fopen;
use function sprintf;

final class PhpTempStream extends Stream
{
    /**
     * @param int<0, max> $maxmemory
     */
    public function __construct(string $mode = 'r+b', int $maxmemory = 2097152)
    {
        $uri = sprintf('php://temp/maxmemory:%d', $maxmemory);

        parent::__construct(fopen($uri, $mode));
    }
}
