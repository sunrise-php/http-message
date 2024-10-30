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
use function fseek;
use function stream_copy_to_stream;

use const SEEK_SET;

final class PhpInputStream extends Stream
{
    public function __construct()
    {
        parent::__construct(self::copyInput());
    }

    /**
     * @return resource
     */
    private static function copyInput()
    {
        /** @var resource $input */
        $input = fopen('php://input', 'rb');
        /** @var resource $resource */
        $resource = fopen('php://temp', 'r+b');

        stream_copy_to_stream($input, $resource);
        fseek($resource, 0, SEEK_SET);

        return $resource;
    }
}
