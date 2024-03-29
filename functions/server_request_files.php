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
 * Import classes
 */
use Sunrise\Http\Message\Stream\FileStream;

/**
 * Import functions
 */
use function is_array;

/**
 * Import constants
 */
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_NO_FILE;

/**
 * Gets the request's uploaded files
 *
 * Please note that unsent files will not be handled,
 * also note that if a file fails to upload successfully,
 * a stream will not be created for it.
 *
 * @param array|null $files
 *
 * @return array
 *
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
            // It makes no sense to create a stream
            // if the file has not been successfully uploaded.
            $stream = UPLOAD_ERR_OK <> $error ? null : new FileStream($path, 'rb');

            return new UploadedFile($stream, $size, $error, $name, $type);
        }

        $result = [];
        foreach ($path as $key => $_) {
            if (UPLOAD_ERR_NO_FILE <> $error[$key]) {
                $result[$key] = $walker(
                    $path[$key],
                    $size[$key],
                    $error[$key],
                    $name[$key],
                    $type[$key]
                );
            }
        }

        return $result;
    };

    $result = [];
    foreach ($files as $key => $file) {
        if (UPLOAD_ERR_NO_FILE <> $file['error']) {
            $result[$key] = $walker(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );
        }
    }

    return $result;
}
