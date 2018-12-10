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
use Sunrise\Stream\StreamFactory;
use Sunrise\Uri\UriFactory;

/**
 * RequestFactory
 *
 * @link https://www.php-fig.org/psr/psr-17/
 */
class RequestFactory implements RequestFactoryInterface
{

	/**
	 * {@inheritDoc}
	 */
	public function createRequest(string $method, $uri) : RequestInterface
	{
		if (! ($uri instanceof UriInterface))
		{
			$uri = (new UriFactory)
			->createUri($uri);
		}

		$body = (new StreamFactory)
		->createStream();

		return (new Request)
		->withMethod($method)
		->withUri($uri)
		->withBody($body);
	}
}
