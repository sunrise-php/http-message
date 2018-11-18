## HTTP message wrapper for PHP 7.2+ based on RFC-7230, PSR-7 & PSR-17

[![Build Status](https://api.travis-ci.com/sunrise-php/http-message.svg?branch=master)](https://travis-ci.com/sunrise-php/http-message)
[![CodeFactor](https://www.codefactor.io/repository/github/sunrise-php/http-message/badge)](https://www.codefactor.io/repository/github/sunrise-php/http-message)
[![Latest Stable Version](https://poser.pugx.org/sunrise/http-message/v/stable)](https://packagist.org/packages/sunrise/http-message)
[![Total Downloads](https://poser.pugx.org/sunrise/http-message/downloads)](https://packagist.org/packages/sunrise/http-message)
[![License](https://poser.pugx.org/sunrise/http-message/license)](https://packagist.org/packages/sunrise/http-message)

## Awards

[![SymfonyInsight](https://insight.symfony.com/projects/62934e27-3e71-439c-9569-4aa57cdb3f36/big.svg)](https://insight.symfony.com/projects/62934e27-3e71-439c-9569-4aa57cdb3f36)

## Installation

```
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

## Test run

```bash
php vendor/bin/phpunit
```

## Api documentation

https://phpdoc.fenric.ru/

## Useful links

https://tools.ietf.org/html/rfc7230<br>
https://www.php-fig.org/psr/psr-7/<br>
https://www.php-fig.org/psr/psr-17/
