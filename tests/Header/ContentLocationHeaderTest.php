<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentLocationHeader;
use Sunrise\Http\Message\Uri;

class ContentLocationHeaderTest extends TestCase
{
    public function testContracts()
    {
        $uri = new Uri('/');
        $header = new ContentLocationHeader($uri);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $uri = new Uri('/');
        $header = new ContentLocationHeader($uri);

        $this->assertSame('Content-Location', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $uri = new Uri('/');
        $header = new ContentLocationHeader($uri);

        $this->assertSame('/', $header->getFieldValue());
    }

    public function testBuild()
    {
        $uri = new Uri('/');
        $header = new ContentLocationHeader($uri);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $uri = new Uri('/');
        $header = new ContentLocationHeader($uri);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
