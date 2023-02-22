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
use function implode;
use function sprintf;

/**
 * @link https://www.w3.org/TR/CSP3/#csp-header
 */
class ContentSecurityPolicyHeader extends Header
{

    /**
     * Regular Expression for a directive name validation
     *
     * @link https://www.w3.org/TR/CSP3/#framework-directives
     *
     * @var string
     */
    public const VALID_DIRECTIVE_NAME = '/^[0-9A-Za-z\-]+$/';

    /**
     * Regular Expression for a directive value validation
     *
     * @link https://www.w3.org/TR/CSP3/#framework-directives
     *
     * @var string
     */
    public const VALID_DIRECTIVE_VALUE = '/^[\x09\x20-\x2B\x2D-\x3A\x3C-\x7E]*$/';

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * Constructor of the class
     *
     * @param array<array-key, mixed> $parameters
     *
     * @throws InvalidHeaderException
     *         If the parameters aren't valid.
     */
    public function __construct(array $parameters = [])
    {
        // validate and normalize the parameters...
        $parameters = $this->validateParametersByRegex(
            $parameters,
            self::VALID_DIRECTIVE_NAME,
            self::VALID_DIRECTIVE_VALUE
        );

        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-Security-Policy';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(): string
    {
        $directives = [];
        foreach ($this->parameters as $directive => $value) {
            // the directive can be without value,
            // e.g. sandbox, upgrade-insecure-requests, etc.
            if ($value === '') {
                $directives[] = $directive;
                continue;
            }

            $directives[] = sprintf('%s %s', $directive, $value);
        }

        return implode('; ', $directives);
    }
}
