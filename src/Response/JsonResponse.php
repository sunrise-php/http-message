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
use JsonException;

/**
 * Import functions
 */
use function json_encode;

/**
 * Import constants
 */
use const JSON_THROW_ON_ERROR;

/**
 * JSON response
 */
final class JsonResponse extends Response
{

    /**
     * Constructor of the class
     *
     * @param int $statusCode
     * @param mixed $data
     * @param int $flags
     * @param int<1, max> $depth
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $statusCode, $data, int $flags = 0, int $depth = 512)
    {
        parent::__construct($statusCode);

        $this->setBody($this->createBody($data, $flags, $depth));

        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * Creates the response body from the given JSON data
     *
     * @param mixed $data
     * @param int $flags
     * @param int<1, max> $depth
     *
     * @return StreamInterface
     *
     * @throws InvalidArgumentException
     */
    private function createBody($data, int $flags, int $depth): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        try {
            $payload = json_encode($data, $flags | JSON_THROW_ON_ERROR, $depth);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(sprintf(
                'Unable to create JSON response due to invalid JSON data: %s',
                $e->getMessage()
            ), 0, $e);
        }

        $stream = new PhpTempStream('r+b');
        $stream->write($payload);
        $stream->rewind();

        return $stream;
    }
}
