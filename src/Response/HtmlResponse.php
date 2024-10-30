<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Response;

use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Response;
use Sunrise\Http\Message\Stream\PhpTempStream;

use function is_object;
use function is_string;
use function method_exists;

final class HtmlResponse extends Response
{
    /**
     * @param mixed $html
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $statusCode, $html)
    {
        parent::__construct($statusCode);

        $this->setBody(self::createBody($html));
        $this->setHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * @param mixed $html
     *
     * @throws InvalidArgumentException
     */
    private static function createBody($html): StreamInterface
    {
        if ($html instanceof StreamInterface) {
            return $html;
        }

        if (is_object($html) && method_exists($html, '__toString')) {
            $html = (string) $html;
        }

        if (!is_string($html)) {
            throw new InvalidArgumentException('Unable to create the HTML response due to a unexpected HTML type');
        }

        $stream = new PhpTempStream('r+b');
        $stream->write($html);
        $stream->rewind();

        return $stream;
    }
}
