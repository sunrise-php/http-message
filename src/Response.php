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
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Header\HeaderInterface;

/**
 * HTTP Response Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Response extends Message implements ResponseInterface
{

	/**
	 * Status code of the message
	 *
	 * @var int
	 */
	protected $statusCode = 200;

	/**
	 * Reason phrase of the message
	 *
	 * @var string
	 */
	protected $reasonPhrase = 'OK';

	/**
	 * {@inheritDoc}
	 */
	public function getStatusCode() : int
	{
		return $this->statusCode;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getReasonPhrase() : string
	{
		return $this->reasonPhrase;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withStatus($statusCode, $reasonPhrase = '') : ResponseInterface
	{
		$this->validateStatusCode($statusCode);
		$this->validateReasonPhrase($reasonPhrase);

		if ('' === $reasonPhrase)
		{
			$reasonPhrase = PHRASES[$statusCode] ?? 'Unknown Status Code';
		}

		$clone = clone $this;

		$clone->statusCode = $statusCode;
		$clone->reasonPhrase = $reasonPhrase;

		return $clone;
	}

	/**
	 * Validates the given status-code
	 *
	 * @param mixed $statusCode
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.1.2
	 */
	protected function validateStatusCode($statusCode) : void
	{
		if (! \is_int($statusCode))
		{
			throw new \InvalidArgumentException('HTTP status-code must be an integer');
		}
		else if (! ($statusCode >= 100 && $statusCode <= 599))
		{
			throw new \InvalidArgumentException(\sprintf('The given status-code "%d" is not valid', $statusCode));
		}
	}

	/**
	 * Validates the given reason-phrase
	 *
	 * @param mixed $reasonPhrase
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.1.2
	 */
	protected function validateReasonPhrase($reasonPhrase) : void
	{
		if (! \is_string($reasonPhrase))
		{
			throw new \InvalidArgumentException('HTTP reason-phrase must be a string');
		}
		else if (! \preg_match(HeaderInterface::RFC7230_FIELD_VALUE, $reasonPhrase))
		{
			throw new \InvalidArgumentException(\sprintf('The given reason-phrase "%s" is not valid', $reasonPhrase));
		}
	}
}
