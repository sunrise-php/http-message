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
use function is_resource;
use function sprintf;

/**
 * FileStream
 */
final class FileStream extends Stream
{

    /**
     * Constructor of the class
     *
     * @param string $filename
     * @param string $mode
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $filename, string $mode)
    {
        $resource = @fopen($filename, $mode);

        if (!is_resource($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to open the file "%s" in the mode "%s"',
                $filename,
                $mode
            ));
        }

        parent::__construct($resource);
    }
}
