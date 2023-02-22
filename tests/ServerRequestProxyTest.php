<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Message\ServerRequest;
use Sunrise\Http\Message\ServerRequestProxy;

class ServerRequestProxyTest extends BaseServerRequestTest
{
    protected function createSubject(): ServerRequestInterface
    {
        return ServerRequestProxy::create(new ServerRequest());
    }
}
