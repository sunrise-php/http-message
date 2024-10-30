# HTTP message wrapper for PHP 7.4+ based on RFC-7230, PSR-7 and PSR-17

[![Build Status](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/build.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/?branch=master)
[![Total Downloads](https://poser.pugx.org/sunrise/http-message/downloads?format=flat)](https://packagist.org/packages/sunrise/http-message)
[![Latest Stable Version](https://poser.pugx.org/sunrise/http-message/v/stable?format=flat)](https://packagist.org/packages/sunrise/http-message)
[![License](https://poser.pugx.org/sunrise/http-message/license?format=flat)](https://packagist.org/packages/sunrise/http-message)

---

## Installation

```bash
composer require sunrise/http-message
```

## Documentation Navigation

- [Server Request from Global Environment](#server-request-from-global-environment)
- [Typed Messages](#typed-messages)
- [Streams](#streams)
- - [File Stream](#file-stream)
- - [PHP Input Stream](#php-input-stream)
- - [PHP Memory Stream](#php-memory-stream)
- - [PHP Temporary Stream](#php-temporary-stream)
- - [Temporary File Stream](#temporary-file-stream)
- [PSR-7 and PSR-17](#psr-7-and-psr-17)
- [Exceptions](#exceptions)

## How to Use

We highly recommend that you study [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-17](https://www.php-fig.org/psr/psr-17/) because only superficial examples will be presented below.

### Server Request from Global Environment

```php
use Sunrise\Http\Message\ServerRequestFactory;

$request = ServerRequestFactory::fromGlobals();
```

### Typed Messages

#### JSON Request

```php
use Sunrise\Http\Message\Request\JsonRequest;

/** @var $data mixed */

$request = new JsonRequest('GET', '/', $data);
```

You can also specify [encoding flags](https://www.php.net/manual/en/json.constants.php#constant.json-hex-tag) and maximum nesting depth like below:

```php
$request = new JsonRequest('GET', '/', $data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE, 512);
```

#### URL Encoded Request

```php
use Sunrise\Http\Message\Request\UrlEncodedRequest;

/** @var $data mixed */

$request = new UrlEncodedRequest('GET', '/', $data);
```

You can also specify [encoding type](https://www.php.net/manual/ru/url.constants.php#constant.php-query-rfc1738) like below:

```php
use Sunrise\Http\Message\Request\UrlEncodedRequest;

$encodingType = UrlEncodedRequest::ENCODING_TYPE_RFC1738;
// or
$encodingType = UrlEncodedRequest::ENCODING_TYPE_RFC3986;

$request = new UrlEncodedRequest('GET', '/', $data, $encodingType);
```

#### JSON Response

```php
use Sunrise\Http\Message\Response\JsonResponse;

/** @var $data mixed */

$response = new JsonResponse(200, $data);
```

You can also specify [encoding flags](https://www.php.net/manual/en/json.constants.php#constant.json-hex-tag) and maximum nesting depth like below:

```php
$response = new JsonResponse(200, $data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE, 512);
```

#### HTML Response

```php
use Sunrise\Http\Message\Response\HtmlResponse;

/** @var $html string|Stringable */

$response = new HtmlResponse(200, $html);
```

### Streams

#### File Stream

```php
use Sunrise\Http\Message\Stream\FileStream;

$fileStream = new FileStream('/folder/file', 'r+b');
```

#### PHP Input Stream

More details about the stream at the [official page](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input).

```php
use Sunrise\Http\Message\Stream\PhpInputStream;

$inputStream = new PhpInputStream();
```

#### PHP Memory Stream

More details about the stream at the [official page](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.memory).

```php
use Sunrise\Http\Message\Stream\PhpMemoryStream;

$memoryStream = new PhpMemoryStream('r+b');
```

#### PHP Temporary Stream

More details about the stream at the [official page](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.memory).

```php
use Sunrise\Http\Message\Stream\PhpTempStream;

$tempStream = new PhpTempStream('r+b');
```

You can also specify the memory limit; when the limit is reached, PHP will start using the temporary file instead of memory.

> Please note that the default memory limit is 2MB.

```php
$maxMemory = 1e+6; // 1MB

$tempStream = new PhpTempStream('r+b', $maxMemory);
```

#### Temporary File Stream

More details about the temporary file behaviour at [the official page](https://www.php.net/manual/en/function.tmpfile).

The stream opens a unique temporary file in binary read/write (w+b) mode. The file will be automatically deleted when it is closed or the program terminates.

```php
use Sunrise\Http\Message\Stream\TmpfileStream;

$tmpfileStream = new TmpfileStream();

// Returns the file path...
$tmpfileStream->getMetadata('uri');
```

If you don't need the above behavior, you can use another temporary file stream:

```php
use Sunrise\Http\Message\Stream\TempFileStream;

$tempFileStream = new TempFileStream();

// Returns the file path...
$tempFileStream->getMetadata('uri');
```

### PSR-7 and PSR-17

The following classes implement PSR-7:

- `Sunrise\Http\Message\Request`
- `Sunrise\Http\Message\Response`
- `Sunrise\Http\Message\ServerRequest`
- `Sunrise\Http\Message\Stream`
- `Sunrise\Http\Message\UploadedFile`
- `Sunrise\Http\Message\Uri`

The following classes implement PSR-17:

- `Sunrise\Http\Message\RequestFactory`
- `Sunrise\Http\Message\ResponseFactory`
- `Sunrise\Http\Message\ServerRequestFactory`
- `Sunrise\Http\Message\StreamFactory`
- `Sunrise\Http\Message\UploadedFileFactory`
- `Sunrise\Http\Message\UriFactory`

### Exceptions

Any exceptions from this package can be caught through the interface:

```php
use Sunrise\Http\Message\Exception\ExceptionInterface;

try {
    // some code...
} catch (ExceptionInterface $e) {
    // some logic...
}
```

---

## Test Run

```bash
composer test
```

## Useful Links

- https://tools.ietf.org/html/rfc7230
- https://www.php-fig.org/psr/psr-7/
- https://www.php-fig.org/psr/psr-17/
