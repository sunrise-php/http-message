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
 * @link https://tools.ietf.org/html/rfc2616#section-14.41
 */
class TransferEncodingHeader extends Header
{

    /**
     * Directives
     *
     * @var string
     */
    public const CHUNKED = 'chunked';
    public const COMPRESS = 'compress';
    public const DEFLATE = 'deflate';
    public const GZIP = 'gzip';

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

        $this->validateToken(...$directives);

        $this->directives = $directives;
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
