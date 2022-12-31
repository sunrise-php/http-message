<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriFactoryInterface;
use Sunrise\Http\Message\UriFactory;

class UriFactoryTest extends TestCase
{
    public function testContracts(): void
    {
        $factory = new UriFactory();

        $this->assertInstanceOf(UriFactoryInterface::class, $factory);
    }

    public function testCreateUriWithUri(): void
    {
        $uri = (new UriFactory)->createUri('/');

        $this->assertSame('/', $uri->getPath());
    }

    public function testCreateUriWithoutUri(): void
    {
        $uri = (new UriFactory)->createUri();

        $this->assertSame('', $uri->getPath());
    }

    public function testCreateUriWithEmptyUri(): void
    {
        $uri = (new UriFactory)->createUri('');

        $this->assertSame('', $uri->getPath());
    }
}
