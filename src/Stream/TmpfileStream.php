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
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\Stream;

/**
 * Import functions
 */
use function is_resource;
use function is_writable;
use function sys_get_temp_dir;
use function tmpfile;

/**
 * The tmpfile() function opens a unique temporary file in binary
 * read/write (w+b) mode. The file will be automatically deleted
 * when it is closed or the program terminates.
 *
 * @link https://www.php.net/tmpfile
 */
final class TmpfileStream extends Stream
{

    /**
     * Constructor of the class
     *
     * @throws RuntimeException
     */
    public function __construct()
    {
        $dirname = sys_get_temp_dir();
        if (!is_writable($dirname)) {
            throw new RuntimeException('Temporary files directory is not writable');
        }

        $resource = tmpfile();
        if (!is_resource($resource)) {
            throw new RuntimeException('Temporary file cannot be created or opened');
        }

        parent::__construct($resource);
    }
}
