<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Integration;

use Http\Psr7Test\ServerRequestIntegrationTest as BaseServerRequestIntegrationTest;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Message\ServerRequest;

class ServerRequestIntegrationTest extends BaseServerRequestIntegrationTest
{

    /**
     * {@inheritdoc}
     */
    public function createSubject(): ServerRequestInterface
    {
        return new ServerRequest(
            null, // HTTP version
            null, // method
            null, // URI
            null, // headers
            null, // body
            $_SERVER,
            [], // query params
            $_COOKIE,
            [], // uploaded files
            null, // parsed body
            [] // attributes
        );
    }
}
