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
use function strtoupper;

/**
 * @link https://tools.ietf.org/html/rfc2616#section-14.7
 */
class AllowHeader extends Header
{

    /**
     * @var list<string>
     */
    private array $methods = [];

    /**
     * Constructor of the class
     *
     * @param string ...$methods
     *
     * @throws InvalidHeaderValueException
     *         If one of the methods isn't valid.
     */
    public function __construct(string ...$methods)
    {
        /** @var list<string> $methods */

        $this->validateToken(...$methods);

        // normalize the list of methods...
        foreach ($methods as $method) {
            $this->methods[] = strtoupper($method);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Allow';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        return implode(', ', $this->methods);
    }
}
