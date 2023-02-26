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
     * DateTime format according to RFC-822
     *
     * @link https://www.rfc-editor.org/rfc/rfc822#section-5
     *
     * @var string
     */
    public const RFC822_DATE_TIME_FORMAT = 'D, d M y H:i:s O';

    /**
     * Regular Expression for a token validation according to RFC-7230
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @var string
     */
    public const RFC7230_VALID_TOKEN = '/^[\x21\x23-\x27\x2A\x2B\x2D\x2E\x30-\x39\x41-\x5A\x5E-\x7A\x7C\x7E]+$/';

    /**
     * Regular Expression for a field value validation according to RFC-7230
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @var string
     */
    public const RFC7230_VALID_FIELD_VALUE = '/^[\x09\x20-\x7E\x80-\xFF]*$/';

    /**
     * Regular Expression for a quoted string validation according to RFC-7230
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @var string
     */
    public const RFC7230_VALID_QUOTED_STRING = '/^(?:[\x5C][\x22]|[\x09\x20\x21\x23-\x5B\x5D-\x7E\x80-\xFF])*$/';

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
        return preg_match(self::RFC7230_VALID_TOKEN, $token) === 1;
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
        $this->validateValueByRegex(self::RFC7230_VALID_TOKEN, ...$tokens);
    }

    /**
     * Validates the given field value(s)
     *
     * @param string ...$fieldValues
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If one of the field values isn't valid.
     */
    final protected function validateFieldValue(string ...$fieldValues): void
    {
        $this->validateValueByRegex(self::RFC7230_VALID_FIELD_VALUE, ...$fieldValues);
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
        $this->validateValueByRegex(self::RFC7230_VALID_QUOTED_STRING, ...$quotedStrings);
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
            self::RFC7230_VALID_TOKEN,
            self::RFC7230_VALID_QUOTED_STRING
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

    /**
     * Formats the given date-time object
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    final protected function formatDateTime(DateTimeInterface $dateTime): string
    {
        if ($dateTime instanceof DateTime) {
            return (clone $dateTime)
                ->setTimezone(new DateTimeZone('GMT'))
                ->format(self::RFC822_DATE_TIME_FORMAT);
        }

        return $dateTime
            ->setTimezone(new DateTimeZone('GMT'))
            ->format(self::RFC822_DATE_TIME_FORMAT);
    }
}
