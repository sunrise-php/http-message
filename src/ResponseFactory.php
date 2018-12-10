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
		$body = (new StreamFactory)
		->createStream();

		return (new Response)
		->withStatus($code, $reasonPhrase)
		->withBody($body);
	}
}
