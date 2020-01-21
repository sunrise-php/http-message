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
use Sunrise\Stream\StreamFactory;

/**
 * Import functions
 */
use function json_encode;

/**
 * ResponseFactory
 *
 * @link https://www.php-fig.org/psr/psr-17/
 */
class ResponseFactory implements ResponseFactoryInterface
{

	/**
	 * {@inheritDoc}
	 */
	public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
	{
		$body = (new StreamFactory)->createStream();

		return (new Response)
		->withStatus($code, $reasonPhrase)
		->withBody($body);
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
		$body = (new StreamFactory)->createStream();
		$body->write((string) $content);

		return (new Response)
		->withStatus($status)
		->withHeader('Content-Type', 'text/html; charset=utf-8')
		->withBody($body);
	}

	/**
	 * Creates a JSON response object
	 *
	 * @param int $status
	 * @param mixed $payload
	 * @param int $options
	 *
	 * @return ResponseInterface
	 */
	public function createJsonResponse(int $status, $payload, int $options = 0) : ResponseInterface
	{
		$body = (new StreamFactory)->createStream();
		$body->write(json_encode($payload, $options));

		return (new Response)
		->withStatus($status)
		->withHeader('Content-Type', 'application/json')
		->withBody($body);
	}
}
