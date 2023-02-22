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
use Sunrise\Http\Message\Exception\InvalidHeaderException;
use Sunrise\Http\Message\Exception\InvalidUriException;
use Sunrise\Http\Message\Header;
use Sunrise\Http\Message\Uri;

/**
 * Import functions
 */
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc5988
 */
class LinkHeader extends Header
{

    /**
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * Constructor of the class
     *
     * @param mixed $uri
     * @param array<array-key, mixed> $parameters
     *
     * @throws InvalidUriException
     *         If the URI isn't valid.
     *
     * @throws InvalidHeaderException
     *         If the parameters aren't valid.
     */
    public function __construct($uri, array $parameters = [])
    {
        // validate and normalize the parameters...
        $parameters = $this->validateParameters($parameters);

        $this->uri = Uri::create($uri);
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Link';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $v = sprintf('<%s>', $this->uri->__toString());
        foreach ($this->parameters as $name => $value) {
            $v .= sprintf('; %s="%s"', $name, $value);
        }

        return $v;
    }
}
