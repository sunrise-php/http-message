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
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Header\HeaderInterface;

/**
 * Hypertext Transfer Protocol Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Message implements MessageInterface
{

	/**
	 * Protocol version for the message
	 *
	 * @var string
	 */
	protected $protocolVersion = '1.1';

	/**
	 * Headers of the message
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * Body of the message
	 *
	 * @var null|StreamInterface
	 */
	protected $body;

	/**
	 * {@inheritDoc}
	 */
	public function getProtocolVersion() : string
	{
		return $this->protocolVersion;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withProtocolVersion($protocolVersion) : MessageInterface
	{
		$this->validateProtocolVersion($protocolVersion);

		$clone = clone $this;

		$clone->protocolVersion = $protocolVersion;

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeaders() : array
	{
		return $this->headers;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasHeader($name) : bool
	{
		$name = $this->normalizeHeaderName($name);

		return ! empty($this->headers[$name]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeader($name) : array
	{
		$name = $this->normalizeHeaderName($name);

		if (empty($this->headers[$name]))
		{
			return [];
		}

		return $this->headers[$name];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeaderLine($name) : string
	{
		$name = $this->normalizeHeaderName($name);

		if (empty($this->headers[$name]))
		{
			return '';
		}

		return \implode(', ', $this->headers[$name]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function withHeader($name, $value) : MessageInterface
	{
		$this->validateHeaderName($name);
		$this->validateHeaderValue($value);

		$name = $this->normalizeHeaderName($name);
		$value = $this->normalizeHeaderValue($value);

		$clone = clone $this;

		$clone->headers[$name] = $value;

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withAddedHeader($name, $value) : MessageInterface
	{
		$this->validateHeaderName($name);
		$this->validateHeaderValue($value);

		$name = $this->normalizeHeaderName($name);
		$value = $this->normalizeHeaderValue($value);

		if (! empty($this->headers[$name]))
		{
			$value = \array_merge($this->headers[$name], $value);
		}

		$clone = clone $this;

		$clone->headers[$name] = $value;

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withoutHeader($name) : MessageInterface
	{
		$name = $this->normalizeHeaderName($name);

		$clone = clone $this;

		unset($clone->headers[$name]);

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBody() : ?StreamInterface
	{
		return $this->body;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withBody(StreamInterface $body) : MessageInterface
	{
		$clone = clone $this;

		$clone->body = $body;

		return $clone;
	}

	/**
	 * Validates the given protocol version
	 *
	 * @param mixed $protocolVersion
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-2.6
	 * @link https://tools.ietf.org/html/rfc7540
	 */
	protected function validateProtocolVersion($protocolVersion) : void
	{
		if (! \is_string($protocolVersion))
		{
			throw new \InvalidArgumentException('HTTP protocol version must be a string');
		}
		else if (! \preg_match('/^\d(?:\.\d)?$/', $protocolVersion))
		{
			throw new \InvalidArgumentException(\sprintf('The given protocol version "%s" is not valid', $protocolVersion));
		}
	}

	/**
	 * Validates the given header name
	 *
	 * @param mixed $headerName
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.2
	 */
	protected function validateHeaderName($headerName) : void
	{
		if (! \is_string($headerName))
		{
			throw new \InvalidArgumentException('Header name must be a string');
		}
		else if (! \preg_match(HeaderInterface::RFC7230_TOKEN, $headerName))
		{
			throw new \InvalidArgumentException(\sprintf('The given header name "%s" is not valid', $headerName));
		}
	}

	/**
	 * Validates the given header value
	 *
	 * @param mixed $headerValue
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.2
	 */
	protected function validateHeaderValue($headerValue) : void
	{
		if (\is_string($headerValue))
		{
			$headerValue = [$headerValue];
		}

		if (! \is_array($headerValue) || [] === $headerValue)
		{
			throw new \InvalidArgumentException('Header value must be a string or not an empty array');
		}

		foreach ($headerValue as $oneOf)
		{
			if (! \is_string($oneOf))
			{
				throw new \InvalidArgumentException('Header value must be a string or an array containing only strings');
			}
			else if (! \preg_match(HeaderInterface::RFC7230_FIELD_VALUE, $oneOf))
			{
				throw new \InvalidArgumentException(\sprintf('The given header value "%s" is not valid', $oneOf));
			}
		}
	}

	/**
	 * Normalizes the given header name
	 *
	 * @param string $headerName
	 *
	 * @return string
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.2
	 */
	protected function normalizeHeaderName($headerName) : string
	{
		// Each header field consists of a case-insensitive field name...
		$headerName = \strtolower($headerName);

		return $headerName;
	}

	/**
	 * Normalizes the given header value
	 *
	 * @param string|array $headerValue
	 *
	 * @return array
	 */
	protected function normalizeHeaderValue($headerValue) : array
	{
		$headerValue = (array) $headerValue;

		$headerValue = \array_values($headerValue);

		return $headerValue;
	}
}
