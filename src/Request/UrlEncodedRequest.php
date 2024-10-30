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
use TypeError;

use function gettype;
use function http_build_query;
use function is_array;
use function is_object;
use function sprintf;

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
     * @param array<array-key, mixed>|object $data
     * @param self::ENCODING_TYPE_* $encodingType
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $method, $uri, $data, int $encodingType = self::ENCODING_TYPE_RFC1738)
    {
        /**
         * @psalm-suppress DocblockTypeContradiction
         * @phpstan-ignore-next-line
         */
        if (!is_array($data) && !is_object($data)) {
            throw new TypeError(sprintf(
                'Argument #3 ($data) must be of type string, %s given',
                gettype($data),
            ));
        }

        parent::__construct($method, $uri);

        $this->setBody(self::createBody($data, $encodingType));
        $this->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
    }

    /**
     * @param array<array-key, mixed>|object $data
     * @param self::ENCODING_TYPE_* $encodingType
     */
    private static function createBody($data, int $encodingType): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        $encodedData = http_build_query($data, '', '', $encodingType);

        $stream = new PhpTempStream('r+b');
        $stream->write($encodedData);
        $stream->rewind();

        return $stream;
    }
}
