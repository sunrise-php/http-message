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

use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Stream;
use Throwable;

use function fopen;
use function is_resource;
use function sprintf;

final class FileStream extends Stream
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $filename, string $mode)
    {
        parent::__construct(self::openFile($filename, $mode));
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
     * @throws InvalidArgumentException
     */
    private static function openFile(string $filename, string $mode)
    {
        try {
            $resource = @fopen($filename, $mode);
        } catch (Throwable $e) {
            $resource = false;
        }

        if (!is_resource($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to open the file "%s" in the mode "%s"',
                $filename,
                $mode,
            ));
        }

        return $resource;
    }
}
