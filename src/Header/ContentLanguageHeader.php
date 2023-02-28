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
use Sunrise\Http\Message\Exception\InvalidHeaderException;
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
     * @var list<string>
     */
    private array $languages = [];

    /**
     * Constructor of the class
     *
     * @param string ...$languages
     *
     * @throws InvalidHeaderException
     *         If one of the language codes isn't valid.
     */
    public function __construct(string ...$languages)
    {
        $this->validateToken(...$languages);

        foreach ($languages as $language) {
            $this->languages[] = $language;
        }
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
