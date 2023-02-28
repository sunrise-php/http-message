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
use function explode;
use function sprintf;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.17
 */
class ContentTypeHeader extends Header
{

    /**
     * @var string
     */
    private string $type;

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * Constructor of the class
     *
     * @param string $type
     * @param array<array-key, mixed> $parameters
     *
     * @throws InvalidHeaderException
     *         - If the type isn't valid;
     *         - If the parameters aren't valid.
     */
    public function __construct(string $type, array $parameters = [])
    {
        if (strpos($type, '/') === false) {
            $type .= '/*';
        }

        $this->validateToken(...explode('/', $type, 2));

        $parameters = $this->validateParameters($parameters);

        $this->type = $type;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-Type';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $v = $this->type;
        foreach ($this->parameters as $name => $value) {
            $v .= sprintf('; %s="%s"', $name, $value);
        }

        return $v;
    }
}
