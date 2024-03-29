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
use Sunrise\Http\Message\Exception\InvalidArgumentException;

/**
 * Import functions
 */
use function is_string;
use function preg_replace_callback;
use function rawurlencode;

/**
 * URI component "user"
 *
 * @link https://tools.ietf.org/html/rfc3986#section-3.2.1
 */
final class User implements ComponentInterface
{

    /**
     * Regular expression used for the component normalization
     *
     * @var string
     */
    // phpcs:ignore Generic.Files.LineLength
    private const NORMALIZATION_REGEX = '/(?:%[0-9A-Fa-f]{2}|[\x21\x24\x26-\x2e\x30-\x39\x3b\x3d\x41-\x5a\x5f\x61-\x7a\x7e]+)|(.?)/u';

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
     * @throws InvalidArgumentException
     *         If the component isn't valid.
     */
    public function __construct($value)
    {
        if ($value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('URI component "user" must be a string');
        }

        $this->value = (string) preg_replace_callback(
            self::NORMALIZATION_REGEX,
            static function (array $match): string {
                /** @var array{0: string, 1?: string} $match */
                return isset($match[1]) ? rawurlencode($match[1]) : $match[0];
            },
            $value
        );
    }

    /**
     * Creates a user component
     *
     * @param mixed $user
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public static function create($user): User
    {
        if ($user instanceof User) {
            return $user;
        }

        return new User($user);
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
