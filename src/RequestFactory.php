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
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

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
 * HTTP Request Message Factory
 *
 * @link https://www.php-fig.org/psr/psr-17/
 */
class RequestFactory implements RequestFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function createRequest(string $method, $uri) : RequestInterface
    {
        return new Request($method, $uri);
    }

    /**
     * Creates a JSON request
     *
     * @param string $method
     * @param string|UriInterface|null $uri
     * @param mixed $data
     * @param int $flags
     * @param int $depth
     *
     * @return RequestInterface
     *
     * @throws InvalidArgumentException
     *         If the data cannot be encoded.
     */
    public function createJsonRequest(string $method, $uri, $data, int $flags = 0, int $depth = 512) : RequestInterface
    {
        /**
         * @psalm-suppress UnusedFunctionCall
         */
        json_encode('');

        $json = json_encode($data, $flags, $depth);
        if (JSON_ERROR_NONE <> json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $request = new Request($method, $uri, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);

        $request->getBody()->write($json);

        return $request;
    }
}
