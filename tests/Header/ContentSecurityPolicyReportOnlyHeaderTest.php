<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentSecurityPolicyReportOnlyHeader;

class ContentSecurityPolicyReportOnlyHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([]);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([]);

        $this->assertSame('Content-Security-Policy-Report-Only', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([]);

        $this->assertSame('', $header->getFieldValue());
    }

    public function testParameterWithoutValue()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([
            'foo' => '',
        ]);

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testParameterWithValue()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([
            'foo' => 'bar',
        ]);

        $this->assertSame('foo bar', $header->getFieldValue());
    }

    public function testParameterWithInteger()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([
            'foo' => 1,
        ]);

        $this->assertSame('foo 1', $header->getFieldValue());
    }

    public function testSeveralParameters()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader([
            'foo' => '',
            'bar' => 'bat',
            'baz' => 1,
        ]);

        $this->assertSame('foo; bar bat; baz 1', $header->getFieldValue());
    }

    public function testInvalidParameterName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "name=" for the header "Content-Security-Policy-Report-Only" is not valid'
        );

        new ContentSecurityPolicyReportOnlyHeader(['name=' => 'value']);
    }

    public function testInvalidParameterNameType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "<integer>" for the header "Content-Security-Policy-Report-Only" is not valid'
        );

        new ContentSecurityPolicyReportOnlyHeader([0 => 'value']);
    }

    public function testInvalidParameterValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value ";value" for the header "Content-Security-Policy-Report-Only" is not valid'
        );

        new ContentSecurityPolicyReportOnlyHeader(['name' => ';value']);
    }

    public function testInvalidParameterValueType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value "<array>" for the header "Content-Security-Policy-Report-Only" is not valid'
        );

        new ContentSecurityPolicyReportOnlyHeader(['name' => []]);
    }

    public function testBuild()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader(['foo' => 'bar']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentSecurityPolicyReportOnlyHeader(['foo' => 'bar']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
