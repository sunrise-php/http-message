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

final class PhpMemoryStream extends Stream
{
    public function __construct(string $mode = 'r+b')
    {
        parent::__construct(fopen('php://memory', $mode));
    }
}
