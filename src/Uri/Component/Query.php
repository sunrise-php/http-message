<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message\Uri\Component;

use Sunrise\Http\Message\Exception\InvalidArgumentException;

use function is_string;
use function preg_replace_callback;
use function rawurlencode;

/**
 * @link https://tools.ietf.org/html/rfc3986#section-3.4
 */
final class Query implements ComponentInterface
{
    // phpcs:ignore Generic.Files.LineLength
    private const NORMALIZATION_REGEX = '/(?:%[0-9A-Fa-f]{2}|[\x21\x24\x26-\x3b\x3d\x3f-\x5a\x5f\x61-\x7a\x7e]+)|(.?)/u';

    private string $value = '';

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value)
    {
        if ($value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('URI component "query" must be a string');
        }

        $this->value = (string) preg_replace_callback(
            self::NORMALIZATION_REGEX,
            static fn(array $matches): string => (
                /** @var array{0: string, 1?: string} $matches */
                isset($matches[1]) ? rawurlencode($matches[1]) : $matches[0]
            ),
            $value,
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
