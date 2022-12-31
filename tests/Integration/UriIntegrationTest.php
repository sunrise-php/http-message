<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Integration;

use Http\Psr7Test\UriIntegrationTest as BaseUriIntegrationTest;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Uri;

class UriIntegrationTest extends BaseUriIntegrationTest
{

    /**
     * {@inheritdoc}
     */
    public function createUri($uri): UriInterface
    {
        return new Uri($uri);
    }
}
