<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AllowHeader;

class AllowHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AllowHeader('GET');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AllowHeader('GET');

        $this->assertSame('Allow', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AllowHeader('GET');

        $this->assertSame('GET', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new AllowHeader('HEAD', 'GET', 'POST');

        $this->assertSame('HEAD, GET, POST', $header->getFieldValue());
    }

    public function testValueCapitalizing()
    {
        $header = new AllowHeader('head', 'get', 'post');

        $this->assertSame('HEAD, GET, POST', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Allow" is not valid');

        new AllowHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Allow" is not valid');

        new AllowHeader('head', '', 'post');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Allow" is not valid');

        // isn't a token...
        new AllowHeader('@');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Allow" is not valid');

        // isn't a token...
        new AllowHeader('head', '@', 'post');
    }

    public function testBuild()
    {
        $header = new AllowHeader('GET');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AllowHeader('GET');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
