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
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function implode;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.12
 */
class ContentLanguageHeader extends Header
{

    /**
     * Regular Expression for a language tag validation
     *
     * @link https://tools.ietf.org/html/rfc2616#section-3.10
     *
     * @var string
     */
    public const RFC2616_LANGUAGE_TAG = '/^[a-zA-Z]{1,8}(?:\-[a-zA-Z]{1,8})?$/';

    /**
     * @var list<string>
     */
    private array $languages;

    /**
     * Constructor of the class
     *
     * @param string ...$languages
     *
     * @throws InvalidHeaderValueException
     *         If one of the language codes isn't valid.
     */
    public function __construct(string ...$languages)
    {
        /** @var list<string> $languages */

        $this->validateValueByRegex(self::RFC2616_LANGUAGE_TAG, ...$languages);

        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-Language';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return implode(', ', $this->languages);
    }
}
