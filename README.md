## HTTP message wrapper for PHP 7.1+ based on RFC-7230, PSR-7 & PSR-17

[![Gitter](https://badges.gitter.im/sunrise-php/support.png)](https://gitter.im/sunrise-php/support)
[![Build Status](https://api.travis-ci.com/sunrise-php/http-message.svg?branch=master)](https://travis-ci.com/sunrise-php/http-message)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/http-message/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-message/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/sunrise/http-message/v/stable)](https://packagist.org/packages/sunrise/http-message)
[![Total Downloads](https://poser.pugx.org/sunrise/http-message/downloads)](https://packagist.org/packages/sunrise/http-message)
[![License](https://poser.pugx.org/sunrise/http-message/license)](https://packagist.org/packages/sunrise/http-message)

## Awards

[![SymfonyInsight](https://insight.symfony.com/projects/62934e27-3e71-439c-9569-4aa57cdb3f36/big.svg)](https://insight.symfony.com/projects/62934e27-3e71-439c-9569-4aa57cdb3f36)

## Installation

```bash
composer require sunrise/http-message
```

## How to use?

#### HTTP Request Message

```php
use Sunrise\Http\Message\RequestFactory;

$message = (new RequestFactory)->createRequest('GET', 'http://php.net/');

// just use PSR-7 methods...
```

#### HTTP Response Message

```php
use Sunrise\Http\Message\ResponseFactory;

$message = (new ResponseFactory)->createResponse(200, 'OK');

// just use PSR-7 methods...
```

#### Remaining PSR-7 packages

* https://github.com/sunrise-php/http-server-request
* https://github.com/sunrise-php/stream
* https://github.com/sunrise-php/uri

## Test run

```bash
php vendor/bin/phpunit
```

## Api documentation

https://phpdoc.fenric.ru/

## Useful links

* https://tools.ietf.org/html/rfc7230
* https://www.php-fig.org/psr/psr-7/
* https://www.php-fig.org/psr/psr-17/
