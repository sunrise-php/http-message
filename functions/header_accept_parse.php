<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Nekhay <afenric@gmail.com>
 * @copyright Copyright (c) 2018, Anatoly Nekhay
 * @license https://github.com/sunrise-php/http-message/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-message
 */

namespace Sunrise\Http\Message;

/**
 * Import functions
 */
use function uasort;

/**
 * Parses the given Accept header
 *
 * @param string $header
 *
 * @return array<string, array<string, string>>
 */
function header_accept_parse(string $header): array
{
    static $cache = [];

    if (isset($cache[$header])) {
        return $cache[$header];
    }

    $cursor = -1;
    $cursorInValue = true;
    $cursorInParameter = false;
    $cursorInParameterName = false;
    $cursorInParameterValue = false;
    $cursorInQuotedParameterValue = false;

    $data = [];
    $valueIndex = 0;
    $parameterIndex = 0;

    while (true) {
        $char = $header[++$cursor] ?? null;
        $prev = $header[$cursor-1] ?? null;
        $next = $header[$cursor+1] ?? null;

        if ($char === null) {
            break;
        }

        if ($char === ' ' && !$cursorInQuotedParameterValue) {
            continue;
        }

        if ($char === ',' && !$cursorInQuotedParameterValue) {
            $cursorInValue = true;
            $cursorInParameter = false;
            $cursorInParameterName = false;
            $cursorInParameterValue = false;
            $cursorInQuotedParameterValue = false;
            $parameterIndex = 0;
            $valueIndex++;
            continue;
        }
        if ($char === ';' && !$cursorInQuotedParameterValue) {
            $cursorInValue = false;
            $cursorInParameter = true;
            $cursorInParameterName = true;
            $cursorInParameterValue = false;
            $cursorInQuotedParameterValue = false;
            $parameterIndex++;
            continue;
        }
        if ($char === '=' && !$cursorInQuotedParameterValue && $cursorInParameterName) {
            $cursorInValue = false;
            $cursorInParameter = true;
            $cursorInParameterName = false;
            $cursorInParameterValue = true;
            $cursorInQuotedParameterValue = false;
            continue;
        }

        if ($char === '"' && !$cursorInQuotedParameterValue && $cursorInParameterValue) {
            $cursorInQuotedParameterValue = true;
            continue;
        }
        if ($char === '\\' && $next === '"' && $cursorInQuotedParameterValue) {
            continue;
        }
        if ($char === '"' && $prev !== '\\' && $cursorInQuotedParameterValue) {
            $cursorInParameterValue = false;
            $cursorInQuotedParameterValue = false;
            continue;
        }

        if ($cursorInValue) {
            $data[$valueIndex][0] ??= '';
            $data[$valueIndex][0] .= $char;
            continue;
        }
        if ($cursorInParameterName && isset($data[$valueIndex][0])) {
            $data[$valueIndex][1][$parameterIndex][0] ??= '';
            $data[$valueIndex][1][$parameterIndex][0] .= $char;
            continue;
        }
        if ($cursorInParameterValue && isset($data[$valueIndex][1][$parameterIndex][0])) {
            $data[$valueIndex][1][$parameterIndex][1] ??= '';
            $data[$valueIndex][1][$parameterIndex][1] .= $char;
            continue;
        }
    }

    $result = [];
    foreach ($data as $item) {
        $result[$item[0]] = [];
        if (isset($item[1])) {
            foreach ($item[1] as $param) {
                $result[$item[0]][$param[0]] = $param[1] ?? '';
            }
        }
    }

    uasort($result, fn(array $a, array $b): int => ($b['q'] ?? 1) <=> ($a['q'] ?? 1));

    return $cache[$header] = $result;
}
