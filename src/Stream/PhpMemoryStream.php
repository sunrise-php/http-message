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
use Sunrise\Http\Message\Stream;

/**
 * Import functions
 */
use function fopen;

/**
 * @link https://www.php.net/manual/en/wrappers.php.php#wrappers.php.memory
 */
class PhpMemoryStream extends Stream
{

    /**
     * Constructor of the class
     *
     * @param string $mode
     */
    public function __construct(string $mode = 'r+b')
    {
        parent::__construct(fopen('php://memory', $mode));
    }
}
