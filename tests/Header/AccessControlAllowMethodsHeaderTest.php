<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AccessControlAllowMethodsHeader;

class AccessControlAllowMethodsHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AccessControlAllowMethodsHeader('GET');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AccessControlAllowMethodsHeader('GET');

        $this->assertSame('Access-Control-Allow-Methods', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AccessControlAllowMethodsHeader('GET');

        $this->assertSame('GET', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new AccessControlAllowMethodsHeader('HEAD', 'GET', 'POST');

        $this->assertSame('HEAD, GET, POST', $header->getFieldValue());
    }

    public function testValueCapitalizing()
    {
        $header = new AccessControlAllowMethodsHeader('head', 'get', 'post');

        $this->assertSame('HEAD, GET, POST', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Access-Control-Allow-Methods" is not valid');

        new AccessControlAllowMethodsHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Access-Control-Allow-Methods" is not valid');

        new AccessControlAllowMethodsHeader('head', '', 'post');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Access-Control-Allow-Methods" is not valid');

        // isn't a token...
        new AccessControlAllowMethodsHeader('@');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Access-Control-Allow-Methods" is not valid');

        // isn't a token...
        new AccessControlAllowMethodsHeader('head', '@', 'post');
    }

    public function testBuild()
    {
        $header = new AccessControlAllowMethodsHeader('GET');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AccessControlAllowMethodsHeader('GET');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
