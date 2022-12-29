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
 * @link https://www.w3.org/TR/CSP3/#cspro-header
 */
class ContentSecurityPolicyReportOnlyHeader extends ContentSecurityPolicyHeader
{

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        return 'Content-Security-Policy-Report-Only';
    }
}
