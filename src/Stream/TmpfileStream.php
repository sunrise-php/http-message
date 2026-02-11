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

use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\FileStreamInterface;
use Sunrise\Http\Message\Stream;

use function is_resource;
use function is_writable;
use function sys_get_temp_dir;
use function tmpfile;

/**
 * The {@see tmpfile()} function opens a unique temporary file in binary
 * read/write (w+b) mode. The file will be automatically deleted
 * when it is closed or the program terminates.
 */
final class TmpfileStream extends Stream implements FileStreamInterface
{
    /**
     * @throws RuntimeException
     */
    public function __construct()
    {
        parent::__construct(self::createFile());
    }

    public function getFilename(): string
    {
        /** @var string $filename */
        $filename = $this->getMetadata('uri');

        return $filename;
    }

    /**
     * @return resource
     *
     * @throws RuntimeException
     */
    private static function createFile()
    {
        $dirname = sys_get_temp_dir();
        if (!is_writable($dirname)) {
            throw new RuntimeException('Temporary files directory is not writable');
        }

        $resource = tmpfile();
        if (!is_resource($resource)) {
            throw new RuntimeException('Temporary file cannot be created or opened');
        }

        return $resource;
    }
}
