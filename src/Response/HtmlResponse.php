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

/**
 * Import classes
 */
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Response;
use Sunrise\Http\Message\Stream\PhpTempStream;

/**
 * Import functions
 */
use function is_object;
use function is_string;
use function method_exists;

/**
 * HTML response
 */
final class HtmlResponse extends Response
{

    /**
     * Constructor of the class
     *
     * @param int $statusCode
     * @param mixed $html
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $statusCode, $html)
    {
        parent::__construct($statusCode);

        $this->setBody($this->createBody($html));

        $this->setHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Creates the response body from the given HTML data
     *
     * @param mixed $html
     *
     * @return StreamInterface
     *
     * @throws InvalidArgumentException
     */
    private function createBody($html): StreamInterface
    {
        if ($html instanceof StreamInterface) {
            return $html;
        }

        if (is_object($html) && method_exists($html, '__toString')) {
            /** @var string */
            $html = $html->__toString();
        }

        if (!is_string($html)) {
            throw new InvalidArgumentException('Unable to create HTML response due to unexpected HTML data');
        }

        $stream = new PhpTempStream('r+b');
        $stream->write($html);
        $stream->rewind();

        return $stream;
    }
}
