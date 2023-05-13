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
final class PhpInputStream extends Stream
{

    /**
     * Constructor of the class
     */
    public function __construct()
    {
        /** @var resource */
        $input = fopen('php://input', 'rb');

        /** @var resource */
        $handle = fopen('php://temp', 'r+b');

        stream_copy_to_stream($input, $handle);

        parent::__construct($handle);

        $this->rewind();
    }
}
