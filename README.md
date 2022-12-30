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

## How to use

⚠️ We highly recommend that you study [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-17](https://www.php-fig.org/psr/psr-17/), because only superficial examples will be presented below.

### Headers as objects

If you want to use headers as objects, then follow the example below:

```php
use Sunrise\Http\Message\HeaderInterface;

final class SomeHeader implements HeaderInterface
{
    // some code...
}

$message->withHeader(...new SomeHeader());
```

or you can extend your header from the base header which contains the necessary methods for validation and formatting:

```php
use Sunrise\Http\Message\Header;

final class SomeHeader extends Header
{
    // some code...
}

$message->withHeader(...new SomeHeader());
```

Below is an example of how you can set cookies using the already implemented [Set-Cookie](https://github.com/sunrise-php/http-message/blob/master/docs/headers.md#Set-Cookie) header:

```php
use Sunrise\Http\Message\Header\SetCookieHeader;

$cookie = new SetCookieHeader('sessionid', '38afes7a8');

$message->withAddedHeader(...$cookie);
```

You can see already implemented headers [here](https://github.com/sunrise-php/http-message/blob/master/docs/headers.md).

---

## Test run

```bash
composer test
```

## Useful links

* https://tools.ietf.org/html/rfc7230
* https://www.php-fig.org/psr/psr-7/
* https://www.php-fig.org/psr/psr-17/
