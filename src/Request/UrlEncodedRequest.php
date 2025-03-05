<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Request;

use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Request;
use Sunrise\Http\Message\Stream\PhpTempStream;

use function http_build_query;

use const PHP_QUERY_RFC1738;
use const PHP_QUERY_RFC3986;

/**
 * @since 3.1.0
 */
final class UrlEncodedRequest extends Request
{
    public const ENCODING_TYPE_RFC1738 = PHP_QUERY_RFC1738;
    public const ENCODING_TYPE_RFC3986 = PHP_QUERY_RFC3986;

    /**
     * @param mixed $uri
     * @param mixed $data
     * @param self::ENCODING_TYPE_* $encodingType
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $method, $uri, $data, int $encodingType = self::ENCODING_TYPE_RFC1738)
    {
        parent::__construct($method, $uri);

        $this->setBody(self::createBody($data, $encodingType));
        $this->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
    }

    /**
     * @param mixed $data
     * @param self::ENCODING_TYPE_* $encodingType
     */
    private static function createBody($data, int $encodingType): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        $query = http_build_query((array) $data, '', '&', $encodingType);

        $stream = new PhpTempStream('r+b');
        $stream->write($query);
        $stream->rewind();

        return $stream;
    }
}
