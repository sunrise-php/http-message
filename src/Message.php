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
		$name = \strtolower($name);

		return ! empty($this->headers[$name]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeader($name) : array
	{
		$name = \strtolower($name);

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
		$name = \strtolower($name);

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
		$name = \strtolower($name);
		$value = (array) $value;

		$this->validateHeaderName($name);
		$this->validateHeaderValues($value);

		$clone = clone $this;

		$clone->headers[$name] = $value;

		return $clone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withAddedHeader($name, $value) : MessageInterface
	{
		$name = \strtolower($name);
		$value = (array) $value;

		$this->validateHeaderName($name);
		$this->validateHeaderValues($value);

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
		$name = \strtolower($name);

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
	 * @param string $protocolVersion
	 *
	 * @return void
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-2.6
	 * @link https://tools.ietf.org/html/rfc7540
	 */
	protected function validateProtocolVersion(string $protocolVersion) : void
	{
		if (! \preg_match('/^\d(?:\.\d)?$/', $protocolVersion))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given protocol version "%s" is not valid', $protocolVersion)
			);
		}
	}

	/**
	 * Validates the given header name
	 *
	 * @param string $headerName
	 *
	 * @return void
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.2
	 */
	protected function validateHeaderName(string $headerName) : void
	{
		if (! \preg_match(RFC7230_TOKEN, $headerName))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given header name "%s" is not valid', $headerName)
			);
		}
	}

	/**
	 * Validates the given header value
	 *
	 * @param string $headerValue
	 *
	 * @return void
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.2
	 */
	protected function validateHeaderValue(string $headerValue) : void
	{
		if (! \preg_match(RFC7230_FIELD_VALUE, $headerValue))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given header value "%s" is not valid', $headerValue)
			);
		}
	}

	/**
	 * Validates the given header values
	 *
	 * @param array $headerValues
	 *
	 * @return void
	 */
	protected function validateHeaderValues(array $headerValues) : void
	{
		foreach ($headerValues as $headerValue)
		{
			$this->validateHeaderValue($headerValue);
		}
	}
}
