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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use InvalidArgumentException;

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
     */
    public function createResponse(int $statusCode = 200, string $reasonPhrase = '') : ResponseInterface
    {
        return new Response($statusCode, $reasonPhrase);
    }

    /**
     * Creates a HTML response instance
     *
     * @param int $status
     * @param mixed $content
     *
     * @return ResponseInterface
     */
    public function createHtmlResponse(int $status, $content) : ResponseInterface
    {
        $content = (string) $content;

        $headers = ['Content-Type' => 'text/html; charset=UTF-8'];

        $response = new Response($status, null, $headers);

        $response->getBody()->write($content);

        return $response;
    }

    /**
     * Creates a JSON response instance
     *
     * @param int $status
     * @param mixed $payload
     * @param int $options
     * @param int $depth
     *
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    public function createJsonResponse(int $status, $payload, int $options = 0, int $depth = 512) : ResponseInterface
    {
        json_encode(''); // reset previous error...
        $content = json_encode($payload, $options, $depth);
        if (JSON_ERROR_NONE <> json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $headers = ['Content-Type' => 'application/json; charset=UTF-8'];

        $response = new Response($status, null, $headers);

        $response->getBody()->write($content);

        return $response;
    }
}
