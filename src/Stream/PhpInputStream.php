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
use function stream_copy_to_stream;

/**
 * @link https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input
 */
class PhpInputStream extends Stream
{

    /**
     * Constructor of the class
     */
    public function __construct()
    {
        $input = fopen('php://input', 'rb');
        $resource = fopen('php://temp', 'r+b');

        stream_copy_to_stream($input, $resource);

        parent::__construct($resource);

        $this->rewind();
    }
}
