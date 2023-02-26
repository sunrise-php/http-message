<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Message\ServerRequest;
use Sunrise\Http\Message\ServerRequestHelper;

class ServerRequestHelperTest extends BaseServerRequestTest
{
    protected function createSubject(): ServerRequestInterface
    {
        return ServerRequestHelper::create(new ServerRequest());
    }
}
