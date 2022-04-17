<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import classes
 */
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Stringable;

/**
 * Import functions
 */
use function json_encode;
use function json_last_error;
use function json_last_error_msg;

/**
 * Import constants
 */
use const JSON_ERROR_NONE;

/**
 * HTTP Response Message Factory
 *
 * @link https://www.php-fig.org/psr/psr-17/
 */
class ResponseFactory implements ResponseFactoryInterface
{

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress ParamNameMismatch
     */
    public function createResponse(int $statusCode = 200, string $reasonPhrase = '') : ResponseInterface
    {
        return new Response($statusCode, $reasonPhrase);
    }

    /**
     * Creates a HTML response
     *
     * @param int $statusCode
     * @param string|StreamInterface|Stringable $html
     *
     * @return ResponseInterface
     */
    public function createHtmlResponse(int $statusCode, $html) : ResponseInterface
    {
        $html = (string) $html;

        $response = new Response($statusCode, null, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);

        $response->getBody()->write($html);

        return $response;
    }

    /**
     * Creates a JSON response
     *
     * @param int $statusCode
     * @param mixed $data
     * @param int $flags
     * @param int $depth
     *
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     *         If the data cannot be encoded.
     */
    public function createJsonResponse(int $statusCode, $data, int $flags = 0, int $depth = 512) : ResponseInterface
    {
        /**
         * @psalm-suppress UnusedFunctionCall
         */
        json_encode('');

        $json = json_encode($data, $flags, $depth);
        if (JSON_ERROR_NONE <> json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $response = new Response($statusCode, null, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);

        $response->getBody()->write($json);

        return $response;
    }
}
