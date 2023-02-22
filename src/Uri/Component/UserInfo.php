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
 * URI component "User Information"
 *
 * @link https://tools.ietf.org/html/rfc3986#section-3.2.1
 */
final class UserInfo implements ComponentInterface
{

    /**
     * URI component "user"
     *
     * @var User
     */
    private User $user;

    /**
     * URI component "password"
     *
     * @var Password|null
     */
    private ?Password $password = null;

    /**
     * Constructor of the class
     *
     * @param mixed $user
     * @param mixed $password
     */
    public function __construct($user, $password = null)
    {
        $this->user = User::create($user);

        if (isset($password)) {
            $this->password = Password::create($password);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getValue(): string
    {
        $value = $this->user->getValue();

        if (isset($this->password)) {
            $value .= ':' . $this->password->getValue();
        }

        return $value;
    }
}
