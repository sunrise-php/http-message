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
use Sunrise\Http\Message\Exception\InvalidHeaderValueException;
use Sunrise\Http\Message\Exception\InvalidUriException;
use Sunrise\Http\Message\Header;
use Sunrise\Http\Message\Uri;

/**
 * Import functions
 */
use function sprintf;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
 */
class AccessControlAllowOriginHeader extends Header
{

    /**
     * @var UriInterface|null
     */
    private ?UriInterface $uri = null;

    /**
     * Constructor of the class
     *
     * @param mixed $uri
     *
     * @throws InvalidUriException
     *         If the URI isn't valid.
     *
     * @throws InvalidHeaderValueException
     *         If the URI isn't valid.
     */
    public function __construct($uri = null)
    {
        if (isset($uri)) {
            $uri = Uri::create($uri);
            $this->validateUri($uri);
            $this->uri = $uri;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Access-Control-Allow-Origin';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        if (!isset($this->uri)) {
            return '*';
        }

        $origin = $this->uri->getScheme() . ':';
        $origin .= '//' . $this->uri->getHost();

        $port = $this->uri->getPort();
        if (isset($port)) {
            $origin .= ':' . $port;
        }

        return $origin;
    }

    /**
     * Validates the given URI
     *
     * @param UriInterface $uri
     *
     * @return void
     *
     * @throws InvalidHeaderValueException
     *         If the URI isn't valid.
     */
    private function validateUri(UriInterface $uri): void
    {
        if ($uri->getScheme() === '' || $uri->getHost() === '') {
            throw new InvalidHeaderValueException(sprintf(
                'The URI "%2$s" for the header "%1$s" is not valid',
                $this->getFieldName(),
                $uri->__toString()
            ));
        }
    }
}
