<?php

declare(strict_types=1);

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Sunrise\Http\Message\RequestFactory;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Http\Message\ServerRequestFactory;
use Sunrise\Http\Message\StreamFactory;
use Sunrise\Http\Message\UploadedFileFactory;
use Sunrise\Http\Message\UriFactory;

use function DI\create;

return [
    RequestFactoryInterface::class => create(RequestFactory::class),
    ResponseFactoryInterface::class => create(ResponseFactory::class),
    ServerRequestFactoryInterface::class => create(ServerRequestFactory::class),
    StreamFactoryInterface::class => create(StreamFactory::class),
    UploadedFileFactoryInterface::class => create(UploadedFileFactory::class),
    UriFactoryInterface::class => create(UriFactory::class),
];
