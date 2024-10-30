# HTTP message wrapper for PHP 7.4+, based on RFC-7230, PSR-7, and PSR-17.

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

## How to Use

We highly recommend studying [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-17](https://www.php-fig.org/psr/psr-17/), as only basic examples are provided below.

### Server Request from Global Environment

```php
$request = \Sunrise\Http\Message\ServerRequestFactory::fromGlobals();
```

### Typed Messages

#### JSON Request

```php
$request = new \Sunrise\Http\Message\Request\JsonRequest('POST', '/', ['foo' => 'bar']);
```

You can also specify [encoding flags](https://www.php.net/manual/en/json.constants.php#constant.json-hex-tag) and the maximum nesting depth as shown below:

```php
$request = new \Sunrise\Http\Message\Request\JsonRequest('POST', '/', [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512);
```

#### URL Encoded Request

```php
$request = new \Sunrise\Http\Message\Request\UrlEncodedRequest('POST', '/', ['foo' => 'bar']);
```

You can also specify the [encoding type](https://www.php.net/manual/ru/url.constants.php#constant.php-query-rfc1738) as shown below:

```php
$rfc1738 = \Sunrise\Http\Message\Request\UrlEncodedRequest::ENCODING_TYPE_RFC1738;
$request = new \Sunrise\Http\Message\Request\UrlEncodedRequest('POST', '/', [], $rfc1738);
```

```php
$rfc3986 = \Sunrise\Http\Message\Request\UrlEncodedRequest::ENCODING_TYPE_RFC3986;
$request = new \Sunrise\Http\Message\Request\UrlEncodedRequest('POST', '/', [], $rfc3986);
```

#### JSON Response

```php
$response = new \Sunrise\Http\Message\Response\JsonResponse(200, ['foo' => 'bar']);
```

You can also specify [encoding flags](https://www.php.net/manual/en/json.constants.php#constant.json-hex-tag) and the maximum nesting depth as shown below:

```php
$response = new \Sunrise\Http\Message\Response\JsonResponse(200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512);
```

#### HTML Response

```php
/** @var $html string|Stringable */

$response = new \Sunrise\Http\Message\Response\HtmlResponse(200, $html);
```

### Streams

#### File Stream

```php
$stream = new \Sunrise\Http\Message\Stream\FileStream('/folder/file', 'r+b');
```

#### Input Stream

More details about this stream can be found on the [official page](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input).

```php
$stream = new \Sunrise\Http\Message\Stream\PhpInputStream();
```

#### Memory Stream

More details about this stream can be found on the [official page](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input).

```php
$stream = new \Sunrise\Http\Message\Stream\PhpMemoryStream('r+b');
```

#### Temporary Stream

More details about this stream can be found on the [official page](https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input).

```php
$stream = new \Sunrise\Http\Message\Stream\PhpTempStream('r+b');
```

You can also specify a memory limit. When this limit is reached, PHP will switch to using a temporary file instead of memory.

> Please note that the default memory limit is 2MB.

```php
$maxMemory = 1e+6; // 1MB

$stream = new PhpTempStream('r+b', $maxMemory);
```

#### Temporary File Stream

For more details about the behavior of temporary files, visit [the official page](https://www.php.net/manual/en/function.tmpfile).

The stream opens a unique temporary file in binary read/write mode (w+b). The file will be automatically deleted when it is closed or when the program terminates.

```php
$tmpfileStream = new \Sunrise\Http\Message\Stream\TmpfileStream();

// Returns the file path...
$stream->getMetadata('uri');
```

If you don't require the behavior described above, you can use an alternative temporary file stream:

```php
$tempFileStream = new \Sunrise\Http\Message\Stream\TempFileStream();

// Returns the file path...
$stream->getMetadata('uri');
```

### PSR-7 and PSR-17

The following classes are implementations PSR-7:

- `Sunrise\Http\Message\Request`
- `Sunrise\Http\Message\Response`
- `Sunrise\Http\Message\ServerRequest`
- `Sunrise\Http\Message\Stream`
- `Sunrise\Http\Message\UploadedFile`
- `Sunrise\Http\Message\Uri`

The following classes are implementations PSR-17:

- `Sunrise\Http\Message\RequestFactory`
- `Sunrise\Http\Message\ResponseFactory`
- `Sunrise\Http\Message\ServerRequestFactory`
- `Sunrise\Http\Message\StreamFactory`
- `Sunrise\Http\Message\UploadedFileFactory`
- `Sunrise\Http\Message\UriFactory`

### Error Handling

Any exceptions thrown by this package can be caught through the following interface:

```php
try {
} catch (\Sunrise\Http\Message\Exception\ExceptionInterface $e) {
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
