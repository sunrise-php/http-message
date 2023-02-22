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
use Sunrise\Http\Message\Enum\Encoding;
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function implode;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.41
 */
class TransferEncodingHeader extends Header
{

    /**
     * @deprecated Use the {@see Encoding} enum.
     */
    public const CHUNKED = Encoding::CHUNKED;

    /**
     * @deprecated Use the {@see Encoding} enum.
     */
    public const COMPRESS = Encoding::COMPRESS;

    /**
     * @deprecated Use the {@see Encoding} enum.
     */
    public const DEFLATE = Encoding::DEFLATE;

    /**
     * @deprecated Use the {@see Encoding} enum.
     */
    public const GZIP = Encoding::GZIP;

    /**
     * @var list<string>
     */
    private array $directives = [];

    /**
     * Constructor of the class
     *
     * @param string ...$directives
     *
     * @throws InvalidHeaderException
     *         If one of the directives isn't valid.
     */
    public function __construct(string ...$directives)
    {
        $this->validateToken(...$directives);

        foreach ($directives as $directive) {
            $this->directives[] = $directive;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Transfer-Encoding';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return implode(', ', $this->directives);
    }
}
