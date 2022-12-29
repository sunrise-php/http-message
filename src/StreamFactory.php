<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import classes
 */
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Stream\FileStream;
use Sunrise\Http\Message\Stream\PhpTempStream;

/**
 * StreamFactory
 *
 * @link https://www.php-fig.org/psr/psr-17/
 */
class StreamFactory implements StreamFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new PhpTempStream();
        if ($content === '') {
            return $stream;
        }

        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new FileStream($filename, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
