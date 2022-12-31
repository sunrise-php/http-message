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
use function sprintf;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Clear-Site-Data
 */
class ClearSiteDataHeader extends Header
{

    /**
     * @var list<string>
     */
    private array $directives;

    /**
     * Constructor of the class
     *
     * @param string ...$directives
     *
     * @throws InvalidHeaderValueException
     *         If one of the directives isn't valid.
     */
    public function __construct(string ...$directives)
    {
        /** @var list<string> $directives */

        $this->validateQuotedString(...$directives);

        $this->directives = $directives;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Clear-Site-Data';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $segments = [];
        foreach ($this->directives as $directive) {
            $segments[] = sprintf('"%s"', $directive);
        }

        return implode(', ', $segments);
    }
}
