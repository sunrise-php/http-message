<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AccessControlAllowCredentialsHeader;

class AccessControlAllowCredentialsHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AccessControlAllowCredentialsHeader();

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AccessControlAllowCredentialsHeader();

        $this->assertSame('Access-Control-Allow-Credentials', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AccessControlAllowCredentialsHeader();

        $this->assertSame('true', $header->getFieldValue());
    }

    public function testBuild()
    {
        $header = new AccessControlAllowCredentialsHeader();

        $expected = 'Access-Control-Allow-Credentials: true';

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AccessControlAllowCredentialsHeader();

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
