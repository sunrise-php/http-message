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
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Exception\InvalidUriException;
use Sunrise\Http\Message\Header;
use Sunrise\Http\Message\Uri;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.30
 */
class LocationHeader extends Header
{

    /**
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * Constructor of the class
     *
     * @param mixed $uri
     *
     * @throws InvalidUriException
     *         If the URI isn't valid.
     */
    public function __construct($uri)
    {
        $this->uri = Uri::create($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Location';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return $this->uri->__toString();
    }
}
