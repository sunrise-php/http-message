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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * HTTP Request Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Request extends Message implements RequestInterface
{

	/**
	 * Method of the message
	 *
	 * @var string
	 */
	protected $method = 'GET';

	/**
	 * Request target of the message
	 *
	 * @var null|string
	 */
	protected $requestTarget;

	/**
	 * URI of the message
	 *
	 * @var null|UriInterface
	 */
	protected $uri;

	/**
	 * {@inheritDoc}
	 */
	public function getMethod() : string
	{
		return $this->method;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withMethod($method) : RequestInterface
	{
		$this->validateMethod($method);

		$clone = clone $this;

		$clone->method = \strtoupper($method);

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRequestTarget() : string
	{
		if (! (null === $this->requestTarget))
		{
			return $this->requestTarget;
		}

		if (! ($this->uri instanceof UriInterface))
		{
			return '/';
		}

		// https://tools.ietf.org/html/rfc7230#section-5.3.1
		// https://tools.ietf.org/html/rfc7230#section-2.7
		//
		// origin-form = absolute-path [ "?" query ]
		// absolute-path = 1*( "/" segment )
		if (! (0 === \strncmp($this->uri->getPath(), '/', 1)))
		{
			return '/';
		}

		$origin = $this->uri->getPath();

		if (! ('' === $this->uri->getQuery()))
		{
			$origin .= '?' . $this->uri->getQuery();
		}

		return $origin;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withRequestTarget($requestTarget) : RequestInterface
	{
		$this->validateRequestTarget($requestTarget);

		$clone = clone $this;

		$clone->requestTarget = $requestTarget;

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUri() : ?UriInterface
	{
		return $this->uri;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withUri(UriInterface $uri, $preserveHost = false) : RequestInterface
	{
		$clone = clone $this;

		$clone->uri = $uri;

		if ('' === $uri->getHost() || ($preserveHost && $clone->hasHeader('host')))
		{
			return $clone;
		}

		$newhost = $uri->getHost();

		if (! (null === $uri->getPort()))
		{
			$newhost .= ':' . $uri->getPort();
		}

		// Reassigning the "Host" header
		return $clone->withHeader('host', $newhost);
	}

	/**
	 * Validates the given method
	 *
	 * @param string $method
	 *
	 * @return void
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.1.1
	 */
	protected function validateMethod(string $method) : void
	{
		if (! \preg_match(RFC7230_TOKEN, $method))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given method "%s" is not valid', $method)
			);
		}
	}

	/**
	 * Validates the given request-target
	 *
	 * @param string $requestTarget
	 *
	 * @return void
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-5.3
	 */
	protected function validateRequestTarget(string $requestTarget) : void
	{
		// Safe field-value chars without whitespace
		$regex = '/^[\x21-\x7E\x80-\xFF]+$/';

		if (! \preg_match($regex, $requestTarget))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given request-target "%s" is not valid', $requestTarget)
			);
		}
	}
}
