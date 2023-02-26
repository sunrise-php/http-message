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
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

/**
 * Import functions
 */
use function is_int;
use function is_string;
use function preg_match;

/**
 * HTTP Response Message
 *
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Response extends Message implements ResponseInterface, StatusCodeInterface
{

    /**
     * List of Reason Phrases
     *
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @var array<int, string>
     */
    public const REASON_PHRASES = [

        // 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',

        // 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        // 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        // 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        // 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Default response status code
     *
     * @var int
     */
    public const DEFAULT_STATUS_CODE = self::STATUS_OK;

    /**
     * Default response reason phrase
     *
     * @var string
     */
    public const DEFAULT_REASON_PHRASE = self::REASON_PHRASES[self::DEFAULT_STATUS_CODE];

    /**
     * Reason phrase for unknown status code
     *
     * @var string
     */
    public const UNKNOWN_STATUS_CODE_REASON_PHRASE = 'Unknown Status Code';

    /**
     * The response's status code
     *
     * @var int
     */
    private int $statusCode = self::DEFAULT_STATUS_CODE;

    /**
     * The response's reason phrase
     *
     * @var string
     */
    private string $reasonPhrase = self::DEFAULT_REASON_PHRASE;

    /**
     * Constrictor of the class
     *
     * @param int|null $statusCode
     * @param string|null $reasonPhrase
     * @param array<string, string|string[]>|null $headers
     * @param StreamInterface|null $body
     *
     * @throws InvalidArgumentException
     *         If one of the arguments isn't valid.
     */
    public function __construct(
        ?int $statusCode = null,
        ?string $reasonPhrase = null,
        ?array $headers = null,
        ?StreamInterface $body = null
    ) {
        if (isset($statusCode)) {
            $this->setStatus($statusCode, $reasonPhrase ?? '');
        }

        if (isset($headers)) {
            $this->setHeaders($headers);
        }

        if (isset($body)) {
            $this->setBody($body);
        }
    }

    /**
     * Gets the response's status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets the response's reason phrase
     *
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * Creates a new instance of the response with the given status code
     *
     * @param int $code
     * @param string $reasonPhrase
     *
     * @return static
     *
     * @throws InvalidArgumentException
     *         If the status isn't valid.
     */
    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        $clone = clone $this;
        $clone->setStatus($code, $reasonPhrase);

        return $clone;
    }

    /**
     * Sets the given status code to the response
     *
     * @param int $statusCode
     * @param string $reasonPhrase
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the status isn't valid.
     */
    final protected function setStatus($statusCode, $reasonPhrase): void
    {
        $this->validateStatusCode($statusCode);
        $this->validateReasonPhrase($reasonPhrase);

        if ('' === $reasonPhrase) {
            $reasonPhrase = self::REASON_PHRASES[$statusCode] ?? self::UNKNOWN_STATUS_CODE_REASON_PHRASE;
        }

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Validates the given status code
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.1.2
     *
     * @param mixed $statusCode
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the status code isn't valid.
     */
    private function validateStatusCode($statusCode): void
    {
        if (!is_int($statusCode)) {
            throw new InvalidArgumentException('HTTP status code must be an integer');
        }

        if (! ($statusCode >= 100 && $statusCode <= 599)) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }
    }

    /**
     * Validates the given reason phrase
     *
     * @link https://tools.ietf.org/html/rfc7230#section-3.1.2
     *
     * @param mixed $reasonPhrase
     *
     * @return void
     *
     * @throws InvalidArgumentException
     *         If the reason phrase isn't valid.
     */
    private function validateReasonPhrase($reasonPhrase): void
    {
        if ('' === $reasonPhrase) {
            return;
        }

        if (!is_string($reasonPhrase)) {
            throw new InvalidArgumentException('HTTP reason phrase must be a string');
        }

        if (!preg_match(Header::RFC7230_VALID_FIELD_VALUE, $reasonPhrase)) {
            throw new InvalidArgumentException('Invalid HTTP reason phrase');
        }
    }
}
