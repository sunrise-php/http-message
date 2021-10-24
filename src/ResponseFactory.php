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
        $headers = ['Content-Type' => 'text/html; charset=UTF-8'];
        $response = new Response($status, null, $headers);

        $content = (string) $content;
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
     * @throws JsonException
     */
    public function createJsonResponse(int $status, $payload, int $options = 0, int $depth = 512) : ResponseInterface
    {
        $headers = ['Content-Type' => 'application/json; charset=UTF-8'];
        $response = new Response($status, null, $headers);

        $content = json_encode($payload, $options | JSON_THROW_ON_ERROR, $depth);
        $response->getBody()->write($content);

        return $response;
    }
}
