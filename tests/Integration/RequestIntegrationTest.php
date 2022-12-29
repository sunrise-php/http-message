<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Integration;

use Http\Psr7Test\RequestIntegrationTest as BaseRequestIntegrationTest;
use Psr\Http\Message\RequestInterface;
use Sunrise\Http\Message\Request;

class RequestIntegrationTest extends BaseRequestIntegrationTest
{

    /**
     * {@inheritdoc}
     */
    public function createSubject(): RequestInterface
    {
        return new Request();
    }
}
