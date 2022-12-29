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
use Sunrise\Http\Message\Exception\InvalidHeaderValueException;
use Sunrise\Http\Message\Exception\InvalidHeaderValueParameterException;
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.17
 */
class ContentTypeHeader extends Header
{

    /**
     * Regular Expression for a content type validation
     *
     * @link https://tools.ietf.org/html/rfc6838#section-4.2
     *
     * @var string
     */
    public const RFC6838_CONTENT_TYPE = '/^[\dA-Za-z][\d\w\!#\$&\+\-\.\^]*(?:\/[\dA-Za-z][\d\w\!#\$&\+\-\.\^]*)?$/';

    /**
     * @var string
     */
    private string $type;

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * Constructor of the class
     *
     * @param string $type
     * @param array<array-key, mixed> $parameters
     *
     * @throws InvalidHeaderValueException
     *         If the type isn't valid.
     *
     * @throws InvalidHeaderValueParameterException
     *         If the parameters aren't valid.
     */
    public function __construct(string $type, array $parameters = [])
    {
        $this->validateValueByRegex(self::RFC6838_CONTENT_TYPE, $type);

        $parameters = $this->validateParameters($parameters);

        $this->type = $type;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-Type';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $v = $this->type;
        foreach ($this->parameters as $name => $value) {
            $v .= sprintf('; %s="%s"', $name, $value);
        }

        return $v;
    }
}
