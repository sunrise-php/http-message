# HTTP message wrapper for PHP 7.1+ based on RFC-7230, PSR-7 and PSR-17

[![Build Status](https://circleci.com/gh/sunrise-php/http-message.svg?style=shield)](https://circleci.com/gh/sunrise-php/http-message)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/?branch=master)
[![Total Downloads](https://poser.pugx.org/sunrise/http-message/downloads?format=flat)](https://packagist.org/packages/sunrise/http-message)
[![Latest Stable Version](https://poser.pugx.org/sunrise/http-message/v/stable?format=flat)](https://packagist.org/packages/sunrise/http-message)
[![License](https://poser.pugx.org/sunrise/http-message/license?format=flat)](https://packagist.org/packages/sunrise/http-message)

---

## Installation

```bash
composer require 'sunrise/http-message:^2.0'
```

## How to use?

#### Request message

```php
use Sunrise\Http\Message\RequestFactory;

$message = (new RequestFactory)->createRequest('GET', 'http://php.net/');

// just use PSR-7 methods...
```

#### Response message

```php
use Sunrise\Http\Message\ResponseFactory;

$message = (new ResponseFactory)->createResponse(200, 'OK');

// just use PSR-7 methods...
```

#### Related packages

* https://github.com/sunrise-php/http-server-request
* https://github.com/sunrise-php/stream
* https://github.com/sunrise-php/uri

#### Headers as objects

* https://github.com/sunrise-php/http-header-kit

---

## Test run

```bash
composer test
```

## Useful links

* https://tools.ietf.org/html/rfc7230
* https://www.php-fig.org/psr/psr-7/
* https://www.php-fig.org/psr/psr-17/
