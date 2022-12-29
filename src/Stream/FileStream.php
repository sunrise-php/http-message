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
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\Stream;
use Throwable;

/**
 * Import functions
 */
use function fopen;
use function is_resource;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;

/**
 * FileStream
 */
class FileStream extends Stream
{

    /**
     * Constructor of the class
     *
     * @param string $filename
     * @param string $mode
     *
     * @throws RuntimeException
     */
    public function __construct(string $filename, string $mode)
    {
        try {
            $resource = fopen($filename, $mode);
        } catch (Throwable $e) {
            $resource = false;
        }

        if (!is_resource($resource)) {
            throw new RuntimeException(sprintf(
                'Unable to open the file "%s" in the mode "%s"',
                $filename,
                $mode
            ));
        }

        parent::__construct($resource);
    }

    /**
     * Creates a new temporary file in the temporary directory
     *
     * @return StreamInterface
     *
     * @throws RuntimeException
     */
    public static function tempFile(): StreamInterface
    {
        $filename = tempnam(sys_get_temp_dir(), 'sunrisephp');

        return new self($filename, 'w+b');
    }
}
