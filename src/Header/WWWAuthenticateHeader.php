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
use Sunrise\Http\Message\Enum\AuthenticationScheme;
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function implode;
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc7235#section-4.1
 */
class WWWAuthenticateHeader extends Header
{

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_BASIC = AuthenticationScheme::BASIC;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_BEARER = AuthenticationScheme::BEARER;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_DIGEST = AuthenticationScheme::DIGEST;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_HOBA = AuthenticationScheme::HOBA;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_MUTUAL = AuthenticationScheme::MUTUAL;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_NEGOTIATE = AuthenticationScheme::NEGOTIATE;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_OAUTH = AuthenticationScheme::OAUTH;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_SCRAM_SHA_1 = AuthenticationScheme::SCRAM_SHA_1;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_SCRAM_SHA_256 = AuthenticationScheme::SCRAM_SHA_256;

    /**
     * @deprecated Use the {@see AuthenticationScheme} enum.
     */
    public const HTTP_AUTHENTICATE_SCHEME_VAPID = AuthenticationScheme::VAPID;

    /**
     * @var string
     */
    private string $scheme;

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * Constructor of the class
     *
     * @param string $scheme
     * @param array<array-key, mixed> $parameters
     *
     * @throws InvalidHeaderException
     *         - If the scheme isn't valid;
     *         - If the parameters aren't valid.
     */
    public function __construct(string $scheme, array $parameters = [])
    {
        $this->validateToken($scheme);

        // validate and normalize the parameters...
        $parameters = $this->validateParameters($parameters);

        $this->scheme = $scheme;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'WWW-Authenticate';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $v = $this->scheme;

        $challenge = [];
        foreach ($this->parameters as $name => $value) {
            $challenge[] = sprintf(' %s="%s"', $name, $value);
        }

        if (!empty($challenge)) {
            $v .= implode(',', $challenge);
        }

        return $v;
    }
}
