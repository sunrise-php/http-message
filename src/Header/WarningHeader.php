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
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;
use Sunrise\Http\Message\HeaderUtils;

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
     * HTTP Warning Codes
     *
     * @link https://www.iana.org/assignments/http-warn-codes/http-warn-codes.xhtml
     */
    public const HTTP_WARNING_CODE_RESPONSE_IS_STALE = 110;
    public const HTTP_WARNING_CODE_REVALIDATION_FAILED = 111;
    public const HTTP_WARNING_CODE_DISCONNECTED_OPERATION = 112;
    public const HTTP_WARNING_CODE_HEURISTIC_EXPIRATION = 113;
    public const HTTP_WARNING_CODE_MISCELLANEOUS_WARNING = 199;
    public const HTTP_WARNING_CODE_TRANSFORMATION_APPLIED = 214;
    public const HTTP_WARNING_CODE_MISCELLANEOUS_PERSISTENT_WARNING = 299;

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
            $value .= sprintf(' "%s"', HeaderUtils::formatDate($this->date));
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
