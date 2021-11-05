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
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
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
     * Creates JSON request
     *
     * @param string $method
     * @param string|UriInterface|null $uri
     * @param mixed $data
     * @param int $options
     * @param int $depth
     *
     * @return RequestInterface
     *
     * @throws InvalidArgumentException
     *         If the data cannot be encoded.
     */
    public function createJsonRequest(
        string $method,
        $uri,
        $data,
        int $options = 0,
        int $depth = 512
    ) : RequestInterface {
        json_encode(''); // reset previous error...
        $content = json_encode($data, $options, $depth);
        if (JSON_ERROR_NONE <> json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $request = new Request($method, $uri, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);

        $request->getBody()->write($content);

        return $request;
    }
}
