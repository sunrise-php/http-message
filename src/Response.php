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
		if ('' === $reasonPhrase)
		{
			$reasonPhrase = PHRASES[$statusCode] ?? '';
		}

		$this->validateStatusCode($statusCode);
		$this->validateReasonPhrase($reasonPhrase);

		$clone = clone $this;

		$clone->statusCode = $statusCode;
		$clone->reasonPhrase = $reasonPhrase;

		return $clone;
	}

	/**
	 * Validates the given status-code
	 *
	 * @param int $statusCode
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.1.2
	 */
	public function validateStatusCode(int $statusCode) : void
	{
		if (! ($statusCode >= 100 && $statusCode <= 599))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given status-code "%d" is not valid', $statusCode)
			);
		}
	}

	/**
	 * Validates the given reason-phrase
	 *
	 * @param string $reasonPhrase
	 *
	 * @throws Exception\InvalidArgumentException
	 *
	 * @link https://tools.ietf.org/html/rfc7230#section-3.1.2
	 */
	public function validateReasonPhrase(string $reasonPhrase) : void
	{
		if (! \preg_match(RFC7230_FIELD_VALUE, $reasonPhrase))
		{
			throw new Exception\InvalidArgumentException(
				\sprintf('The given reason-phrase "%s" is not valid', $reasonPhrase)
			);
		}
	}
}
