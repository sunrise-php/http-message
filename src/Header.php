<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import classes
 */
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Traversable;

/**
 * Import functions
 */
use function gettype;
use function is_int;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * HTTP Header Field
 */
abstract class Header implements HeaderInterface
{

    /**
     * {@inheritdoc}
     */
    final public function getIterator(): Traversable
    {
        yield $this->getFieldName();
        yield $this->getFieldValue();
    }

    /**
     * {@inheritdoc}
     */
    final public function __toString(): string
    {
        return sprintf('%s: %s', $this->getFieldName(), $this->getFieldValue());
    }

    /**
     * Checks if the given string is a token
     *
     * @param string $token
     *
     * @return bool
     */
    final protected function isToken(string $token): bool
    {
        return preg_match(HeaderInterface::RFC7230_TOKEN_REGEX, $token) === 1;
    }

    /**
     * Validates the given token(s)
     *
     * @param string ...$tokens
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If one of the tokens isn't valid.
     */
    final protected function validateToken(string ...$tokens): void
    {
        $this->validateValueByRegex(HeaderInterface::RFC7230_TOKEN_REGEX, ...$tokens);
    }

    /**
     * Validates the given quoted string(s)
     *
     * @param string ...$quotedStrings
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If one of the quoted strings isn't valid.
     */
    final protected function validateQuotedString(string ...$quotedStrings): void
    {
        $this->validateValueByRegex(HeaderInterface::RFC7230_QUOTED_STRING_REGEX, ...$quotedStrings);
    }

    /**
     * Validates and normalizes the given parameters
     *
     * @param array<array-key, mixed> $parameters
     *
     * @return array<string, string>
     *         The normalized parameters.
     *
     * @throws InvalidHeaderException
     *         If one of the parameters isn't valid.
     */
    final protected function validateParameters(array $parameters): array
    {
        return $this->validateParametersByRegex(
            $parameters,
            HeaderInterface::RFC7230_TOKEN_REGEX,
            HeaderInterface::RFC7230_QUOTED_STRING_REGEX
        );
    }

    /**
     * Validates the given value(s) by the given regular expression
     *
     * @param string $regex
     * @param string ...$values
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If one of the values isn't valid.
     */
    final protected function validateValueByRegex(string $regex, string ...$values): void
    {
        foreach ($values as $value) {
            if (!preg_match($regex, $value)) {
                throw new InvalidHeaderException(sprintf(
                    'The value "%2$s" for the header "%1$s" is not valid',
                    $this->getFieldName(),
                    $value
                ));
            }
        }
    }

    /**
     * Validates and normalizes the given parameters by the given regular expressions
     *
     * @param array<array-key, mixed> $parameters
     * @param string $nameRegex
     * @param string $valueRegex
     *
     * @return array<string, string>
     *         The normalized parameters.
     *
     * @throws InvalidHeaderException
     *         If one of the parameters isn't valid.
     */
    final protected function validateParametersByRegex(array $parameters, string $nameRegex, string $valueRegex): array
    {
        foreach ($parameters as $name => &$value) {
            if (!is_string($name) || !preg_match($nameRegex, $name)) {
                throw new InvalidHeaderException(sprintf(
                    'The parameter name "%2$s" for the header "%1$s" is not valid',
                    $this->getFieldName(),
                    (is_string($name) ? $name : ('<' . gettype($name) . '>'))
                ));
            }

            // e.g. Cache-Control: max-age=31536000
            if (is_int($value)) {
                $value = (string) $value;
            }

            if (!is_string($value) || !preg_match($valueRegex, $value)) {
                throw new InvalidHeaderException(sprintf(
                    'The parameter value "%2$s" for the header "%1$s" is not valid',
                    $this->getFieldName(),
                    (is_string($value) ? $value : ('<' . gettype($value) . '>'))
                ));
            }
        }

        /** @var array<string, string> $parameters */

        return $parameters;
    }
}
