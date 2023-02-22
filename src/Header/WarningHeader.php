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
use DateTimeInterface;
use Sunrise\Http\Message\Enum\WarningCode;
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.46
 */
class WarningHeader extends Header
{

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_RESPONSE_IS_STALE = WarningCode::RESPONSE_IS_STALE;

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_REVALIDATION_FAILED = WarningCode::REVALIDATION_FAILED;

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_DISCONNECTED_OPERATION = WarningCode::DISCONNECTED_OPERATION;

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_HEURISTIC_EXPIRATION = WarningCode::HEURISTIC_EXPIRATION;

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_MISCELLANEOUS_WARNING = WarningCode::MISCELLANEOUS_WARNING;

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_TRANSFORMATION_APPLIED = WarningCode::TRANSFORMATION_APPLIED;

    /**
     * @deprecated Use the {@see WarningCode} enum.
     */
    public const HTTP_WARNING_CODE_MISCELLANEOUS_PERSISTENT_WARNING = WarningCode::MISCELLANEOUS_PERSISTENT_WARNING;

    /**
     * @var int
     */
    private int $code;

    /**
     * @var string
     */
    private string $agent;

    /**
     * @var string
     */
    private string $text;

    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $date;

    /**
     * Constructor of the class
     *
     * @param int $code
     * @param string $agent
     * @param string $text
     * @param DateTimeInterface|null $date
     *
     * @throws InvalidHeaderException
     *         If one of arguments isn't valid.
     */
    public function __construct(int $code, string $agent, string $text, ?DateTimeInterface $date = null)
    {
        $this->validateCode($code);
        $this->validateToken($agent);
        $this->validateQuotedString($text);

        $this->code = $code;
        $this->agent = $agent;
        $this->text = $text;
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Warning';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $value = sprintf('%s %s "%s"', $this->code, $this->agent, $this->text);

        if (isset($this->date)) {
            $value .= sprintf(' "%s"', $this->formatDateTime($this->date));
        }

        return $value;
    }

    /**
     * Validates the given code
     *
     * @param int $code
     *
     * @return void
     *
     * @throws InvalidHeaderException
     *         If the code isn't valid.
     */
    private function validateCode(int $code): void
    {
        if (! ($code >= 100 && $code <= 999)) {
            throw new InvalidHeaderException(sprintf(
                'The code "%2$d" for the header "%1$s" is not valid',
                $this->getFieldName(),
                $code
            ));
        }
    }
}
