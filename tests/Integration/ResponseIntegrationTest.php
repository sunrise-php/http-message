<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Integration;

use Http\Psr7Test\ResponseIntegrationTest as BaseResponseIntegrationTest;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Message\Response;

class ResponseIntegrationTest extends BaseResponseIntegrationTest
{

    /**
     * {@inheritdoc}
     */
    public function createSubject(): ResponseInterface
    {
        return new Response();
    }
}
