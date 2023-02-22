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
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Expose-Headers
 */
class AccessControlExposeHeadersHeader extends Header
{

    /**
     * @var list<string>
     */
    private array $headers = [];

    /**
     * Constructor of the class
     *
     * @param string ...$headers
     *
     * @throws InvalidHeaderException
     *         If one of the header names isn't valid.
     */
    public function __construct(string ...$headers)
    {
        $this->validateToken(...$headers);

        foreach ($headers as $header) {
            $this->headers[] = $header;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Access-Control-Expose-Headers';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return implode(', ', $this->headers);
    }
}
