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

/**
 * Import classes
 */
use Sunrise\Http\Message\Exception\InvalidUriComponentException;

/**
 * Import functions
 */
use function is_string;
use function preg_replace_callback;
use function rawurlencode;

/**
 * URI component "password"
 *
 * @link https://tools.ietf.org/html/rfc3986#section-3.2.1
 */
final class Password implements ComponentInterface
{

    /**
     * Regular expression to normalize the component value
     *
     * @var string
     */
    // phpcs:ignore Generic.Files.LineLength
    private const NORMALIZATION_REGEX = '/(?:(?:%[0-9A-Fa-f]{2}|[\x21\x24\x26-\x2e\x30-\x39\x3b\x3d\x41-\x5a\x5f\x61-\x7a\x7e]+)|(.?))/u';

    /**
     * The component value
     *
     * @var string
     */
    private string $value = '';

    /**
     * Constructor of the class
     *
     * @param mixed $value
     *
     * @throws InvalidUriComponentException
     *         If the component isn't valid.
     */
    public function __construct($value)
    {
        if ($value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidUriComponentException('URI component "password" must be a string');
        }

        $this->value = preg_replace_callback(self::NORMALIZATION_REGEX, function (array $match): string {
            /** @var array{0: string, 1?: string} $match */

            return isset($match[1]) ? rawurlencode($match[1]) : $match[0];
        }, $value);
    }

    /**
     * Creates a password component
     *
     * @param mixed $password
     *
     * @return Password
     *
     * @throws InvalidUriComponentException
     */
    public static function create($password): Password
    {
        if ($password instanceof Password) {
            return $password;
        }

        return new Password($password);
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
