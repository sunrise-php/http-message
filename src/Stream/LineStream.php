<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Stream;

use IteratorAggregate;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Exception\RuntimeException;
use Sunrise\Http\Message\Stream;
use Traversable;

use function fgets;
use function is_resource;
use function rtrim;

/**
 * @implements IteratorAggregate<int, string>
 *
 * @since 3.6.0
 */
final class LineStream extends Stream implements IteratorAggregate
{
    /**
     * @var mixed
     */
    private $resource;

    /**
     * @param mixed $resource
     *
     * @throws InvalidArgumentException
     */
    public function __construct($resource, bool $autoClose = true)
    {
        parent::__construct($resource, $autoClose);

        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Stream has no resource');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        while (($line = fgets($this->resource)) !== false) {
            yield rtrim($line);
        }
    }
}
