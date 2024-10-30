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

use Sunrise\Http\Message\Stream\FileStream;

use function is_array;

use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_NO_FILE;

/**
 * @link http://php.net/manual/en/reserved.variables.files.php
 * @link https://www.php.net/manual/ru/features.file-upload.post-method.php
 * @link https://www.php.net/manual/ru/features.file-upload.multiple.php
 * @link https://github.com/php/php-src/blob/8c5b41cefb88b753c630b731956ede8d9da30c5d/main/rfc1867.c
 */
function server_request_files(?array $files = null): array
{
    $files ??= $_FILES;

    $walker = static function ($path, $size, $error, $name, $type) use (&$walker) {
        if (!is_array($path)) {
            $stream = $error === UPLOAD_ERR_OK ? new FileStream($path, 'rb') : null;

            return new UploadedFile($stream, $size, $error, $name, $type);
        }

        $result = [];
        foreach ($path as $key => $_) {
            if ($error[$key] !== UPLOAD_ERR_NO_FILE) {
                $result[$key] = $walker(
                    $path[$key],
                    $size[$key],
                    $error[$key],
                    $name[$key],
                    $type[$key],
                );
            }
        }

        return $result;
    };

    $result = [];
    foreach ($files as $key => $file) {
        if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $result[$key] = $walker(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type'],
            );
        }
    }

    return $result;
}
