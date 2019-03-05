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
use Sunrise\Http\Header\HeaderInterface;

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
	 * @param mixed $method
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.1.1
	 */
	protected function validateMethod($method) : void
	{
		if (! \is_string($method))
		{
			throw new \InvalidArgumentException('HTTP method must be a string');
		}
		else if (! \preg_match(HeaderInterface::RFC7230_TOKEN, $method))
		{
			throw new \InvalidArgumentException(\sprintf('The given method "%s" is not valid', $method));
		}
	}

	/**
	 * Validates the given request-target
	 *
	 * @param mixed $requestTarget
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-5.3
	 */
	protected function validateRequestTarget($requestTarget) : void
	{
		if (! \is_string($requestTarget))
		{
			throw new \InvalidArgumentException('HTTP request-target must be a string');
		}
		else if (! \preg_match('/^[\x21-\x7E\x80-\xFF]+$/', $requestTarget))
		{
			throw new \InvalidArgumentException(\sprintf('The given request-target "%s" is not valid', $requestTarget));
		}
	}
}
