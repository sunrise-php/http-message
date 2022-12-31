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
 * HTML Response
 */
class HtmlResponse extends Response
{

    /**
     * The response content type
     *
     * @var string
     */
    public const CONTENT_TYPE = 'text/html; charset=utf-8';

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
        $body = $this->createBody($html);

        $headers = ['Content-Type' => self::CONTENT_TYPE];

        parent::__construct($statusCode, null, $headers, $body);
    }

    /**
     * Creates the response body from the given HTML
     *
     * @param mixed $html
     *
     * @return StreamInterface
     *
     * @throws InvalidArgumentException
     *         If the response body cannot be created from the given HTML.
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
            throw new InvalidArgumentException('Unable to create HTML response due to invalid body');
        }

        $stream = new PhpTempStream('r+b');
        $stream->write($html);
        $stream->rewind();

        return $stream;
    }
}
