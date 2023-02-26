<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Header;

/**
 * Import classes
 */
use DateTimeImmutable;
use DateTimeInterface;
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function max;
use function rawurlencode;
use function sprintf;
use function strpbrk;
use function time;

/**
 * @link https://tools.ietf.org/html/rfc6265#section-4.1
 * @link https://github.com/php/php-src/blob/master/ext/standard/head.c
 */
class SetCookieHeader extends Header
{

    /**
     * Acceptable the SameSite attribute values
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
     */
    public const SAME_SITE_LAX = 'Lax';
    public const SAME_SITE_STRICT = 'Strict';
    public const SAME_SITE_NONE = 'None';

    /**
     * Cookie option keys
     */
    public const OPTION_KEY_PATH = 'path';
    public const OPTION_KEY_DOMAIN = 'domain';
    public const OPTION_KEY_SECURE = 'secure';
    public const OPTION_KEY_HTTP_ONLY = 'httpOnly';
    public const OPTION_KEY_SAMESITE = 'sameSite';

    /**
     * Default cookie options
     *
     * @var array{path?: ?string, domain?: ?string, secure?: ?bool, httpOnly?: ?bool, sameSite?: ?string}
     */
    protected static array $defaultOptions = [
        self::OPTION_KEY_PATH      => '/',
        self::OPTION_KEY_DOMAIN    => null,
        self::OPTION_KEY_SECURE    => null,
        self::OPTION_KEY_HTTP_ONLY => true,
        self::OPTION_KEY_SAMESITE  => self::SAME_SITE_LAX,
    ];

    /**
     * The cookie name
     *
     * @var string
     */
    private string $name;

    /**
     * The cookie value
     *
     * @var string
     */
    private string $value;

    /**
     * The cookie's expiration date
     *
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $expires;

    /**
     * The cookie options
     *
     * @var array{path?: ?string, domain?: ?string, secure?: ?bool, httpOnly?: ?bool, sameSite?: ?string}
     */
    private array $options;

    /**
     * Constructor of the class
     *
     * @param string $name
     * @param string $value
     * @param DateTimeInterface|null $expires
     * @param array{path?: ?string, domain?: ?string, secure?: ?bool, httpOnly?: ?bool, sameSite?: ?string} $options
     *
     * @throws InvalidHeaderException
     *         If one of the arguments isn't valid.
     */
    public function __construct(string $name, string $value, ?DateTimeInterface $expires = null, array $options = [])
    {
        $this->validateCookieName($name);

        if (isset($options[self::OPTION_KEY_PATH])) {
            $this->validateCookieOption(
                self::OPTION_KEY_PATH,
                $options[self::OPTION_KEY_PATH]
            );
        }

        if (isset($options[self::OPTION_KEY_DOMAIN])) {
            $this->validateCookieOption(
                self::OPTION_KEY_DOMAIN,
                $options[self::OPTION_KEY_DOMAIN]
            );
        }

        if (isset($options[self::OPTION_KEY_SAMESITE])) {
            $this->validateCookieOption(
                self::OPTION_KEY_SAMESITE,
                $options[self::OPTION_KEY_SAMESITE]
            );
        }

        if ($value === '') {
            $value = 'deleted';
            $expires = new DateTimeImmutable('1 year ago');
        }

        $options += static::$defaultOptions;

        $this->name = $name;
        $this->value = $value;
        $this->expires = $expires;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Set-Cookie';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $name = rawurlencode($this->name);
        $value = rawurlencode($this->value);
        $result = sprintf('%s=%s', $name, $value);

        if (isset($this->expires)) {
            $result .= '; Expires=' . $this->formatDateTime($this->expires);
            $result .= '; Max-Age=' . max($this->expires->getTimestamp() - time(), 0);
        }

        if (isset($this->options[self::OPTION_KEY_PATH])) {
            $result .= '; Path=' . $this->options[self::OPTION_KEY_PATH];
        }

        if (isset($this->options[self::OPTION_KEY_DOMAIN])) {
            $result .= '; Domain=' . $this->options[self::OPTION_KEY_DOMAIN];
        }

        if (isset($this->options[self::OPTION_KEY_SECURE]) && $this->options[self::OPTION_KEY_SECURE]) {
            $result .= '; Secure';
        }

        if (isset($this->options[self::OPTION_KEY_HTTP_ONLY]) && $this->options[self::OPTION_KEY_HTTP_ONLY]) {
            $result .= '; HttpOnly';
        }

        if (isset($this->options[self::OPTION_KEY_SAMESITE])) {
            $result .= '; SameSite=' . $this->options[self::OPTION_KEY_SAMESITE];
        }

        return $result;
    }

    /**
     * Validates the given cookie name
     *
     * @param string $name
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If the cookie name isn't valid.
     */
    private function validateCookieName(string $name): void
    {
        if ('' === $name) {
            throw new InvalidHeaderException('Cookie name cannot be empty');
        }

        // https://github.com/php/php-src/blob/02a5335b710aa36cd0c3108bfb9c6f7a57d40000/ext/standard/head.c#L93
        if (strpbrk($name, "=,; \t\r\n\013\014") !== false) {
            throw new InvalidHeaderException(sprintf(
                'The cookie name "%s" contains prohibited characters',
                $name
            ));
        }
    }

    /**
     * Validates the given cookie option
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If the cookie option isn't valid.
     */
    private function validateCookieOption(string $key, $value): void
    {
        if (!is_string($value)) {
            throw new InvalidHeaderException(sprintf(
                'The cookie option "%s" must be a string',
                $key
            ));
        }

        // https://github.com/php/php-src/blob/02a5335b710aa36cd0c3108bfb9c6f7a57d40000/ext/standard/head.c#L103
        // https://github.com/php/php-src/blob/02a5335b710aa36cd0c3108bfb9c6f7a57d40000/ext/standard/head.c#L108
        if (strpbrk($value, ",; \t\r\n\013\014") !== false) {
            throw new InvalidHeaderException(sprintf(
                'The cookie option "%s" contains prohibited characters',
                $key
            ));
        }
    }
}
