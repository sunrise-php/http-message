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
 * JSON Response
 */
class JsonResponse extends Response
{

    /**
     * The response content type
     *
     * @var string
     */
    public const CONTENT_TYPE = 'application/json; charset=utf-8';

    /**
     * Constructor of the class
     *
     * @param int $statusCode
     * @param mixed $data
     * @param int $flags
     * @param int $depth
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $statusCode, $data, int $flags = 0, int $depth = 512)
    {
        $body = $this->createBody($data, $flags, $depth);

        $headers = ['Content-Type' => self::CONTENT_TYPE];

        parent::__construct($statusCode, null, $headers, $body);
    }

    /**
     * Creates the response body from the given JSON data
     *
     * @param mixed $data
     * @param int $flags
     * @param int $depth
     *
     * @return StreamInterface
     *
     * @throws InvalidArgumentException
     *         If the response body cannot be created from the given JSON data.
     */
    private function createBody($data, int $flags, int $depth): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        $flags |= JSON_THROW_ON_ERROR;

        try {
            $payload = json_encode($data, $flags, $depth);
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
