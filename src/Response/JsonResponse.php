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

use JsonException;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Response;
use Sunrise\Http\Message\Stream\PhpTempStream;

use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final class JsonResponse extends Response
{
    /**
     * @param mixed $data
     * @param int<1, max> $depth
     * @psalm-param int<1, 2147483647> $depth
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $statusCode, $data, int $flags = 0, int $depth = 512)
    {
        parent::__construct($statusCode);

        $this->setBody(self::createBody($data, $flags, $depth));
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * @param mixed $data
     * @param int<1, max> $depth
     * @psalm-param int<1, 2147483647> $depth
     *
     * @throws InvalidArgumentException
     */
    private static function createBody($data, int $flags, int $depth): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        try {
            /** @var non-empty-string $json */
            $json = json_encode($data, $flags | JSON_THROW_ON_ERROR, $depth);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(sprintf(
                'Unable to create the JSON response due to an invalid data: %s',
                $e->getMessage()
            ), 0, $e);
        }

        $stream = new PhpTempStream('r+b');
        $stream->write($json);
        $stream->rewind();

        return $stream;
    }
}
