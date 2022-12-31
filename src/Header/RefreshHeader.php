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
 * @link https://en.wikipedia.org/wiki/Meta_refresh
 */
class RefreshHeader extends Header
{

    /**
     * @var int
     */
    private int $delay;

    /**
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * Constructor of the class
     *
     * @param int $delay
     * @param mixed $uri
     *
     * @throws InvalidUriException
     *         If the URI isn't valid.
     *
     * @throws InvalidHeaderValueException
     *         If the delay isn't valid.
     */
    public function __construct(int $delay, $uri)
    {
        $this->validateDelay($delay);

        $uri = Uri::create($uri);

        $this->delay = $delay;
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Refresh';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return sprintf('%d; url=%s', $this->delay, $this->uri->__toString());
    }

    /**
     * Validates the redirection delay
     *
     * @param int $delay
     *
     * @return void
     *
     * @throws InvalidHeaderValueException
     *         If the delay isn't valid.
     */
    private function validateDelay(int $delay): void
    {
        if (! ($delay >= 0)) {
            throw new InvalidHeaderValueException(sprintf(
                'The delay "%2$d" for the header "%1$s" is not valid',
                $this->getFieldName(),
                $delay
            ));
        }
    }
}
