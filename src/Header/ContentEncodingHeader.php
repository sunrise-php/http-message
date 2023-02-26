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
use Sunrise\Http\Message\Dictionary\Encoding;
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Header;

/**
 * Import functions
 */
use function implode;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.11
 */
class ContentEncodingHeader extends Header
{

    /**
     * HTTP Content Encodings
     *
     * @link https://www.iana.org/assignments/http-parameters/http-parameters.xhtml#content-coding
     */
    public const HTTP_CONTENT_ENCODING_AES128GCM = 'aes128gcm';
    public const HTTP_CONTENT_ENCODING_BR = 'br';
    public const HTTP_CONTENT_ENCODING_COMPRESS = 'compress';
    public const HTTP_CONTENT_ENCODING_DEFLATE = 'deflate';
    public const HTTP_CONTENT_ENCODING_GZIP = 'gzip';

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
        return 'Content-Encoding';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return implode(', ', $this->directives);
    }
}
