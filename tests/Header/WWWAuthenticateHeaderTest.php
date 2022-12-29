<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\WWWAuthenticateHeader;

class WWWAuthenticateHeaderTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame('Basic', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_BASIC);
        $this->assertSame('Bearer', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_BEARER);
        $this->assertSame('Digest', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_DIGEST);
        $this->assertSame('HOBA', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_HOBA);
        $this->assertSame('Mutual', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_MUTUAL);
        $this->assertSame('Negotiate', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_NEGOTIATE);
        $this->assertSame('OAuth', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_OAUTH);
        $this->assertSame('SCRAM-SHA-1', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_SCRAM_SHA_1);
        $this->assertSame('SCRAM-SHA-256', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_SCRAM_SHA_256);
        $this->assertSame('vapid', WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_VAPID);
    }

    public function testContracts()
    {
        $header = new WWWAuthenticateHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new WWWAuthenticateHeader('foo');

        $this->assertSame('WWW-Authenticate', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new WWWAuthenticateHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testParameterWithEmptyValue()
    {
        $header = new WWWAuthenticateHeader('foo', [
            'bar' => '',
        ]);

        $this->assertSame('foo bar=""', $header->getFieldValue());
    }

    public function testParameterWithToken()
    {
        $header = new WWWAuthenticateHeader('foo', [
            'bar' => 'token',
        ]);

        $this->assertSame('foo bar="token"', $header->getFieldValue());
    }

    public function testParameterWithQuotedString()
    {
        $header = new WWWAuthenticateHeader('foo', [
            'bar' => 'quoted string',
        ]);

        $this->assertSame('foo bar="quoted string"', $header->getFieldValue());
    }

    public function testParameterWithInteger()
    {
        $header = new WWWAuthenticateHeader('foo', [
            'bar' => 1,
        ]);

        $this->assertSame('foo bar="1"', $header->getFieldValue());
    }

    public function testSeveralParameters()
    {
        $header = new WWWAuthenticateHeader('foo', [
            'bar' => '',
            'baz' => 'token',
            'bat' => 'quoted string',
            'qux' => 1,
        ]);

        $this->assertSame('foo bar="", baz="token", bat="quoted string", qux="1"', $header->getFieldValue());
    }

    public function testEmptyScheme()
    {
        $this->expectException(\InvalidArgumentException::class);

        new WWWAuthenticateHeader('');
    }

    public function testInvalidScheme()
    {
        $this->expectException(\InvalidArgumentException::class);

        // isn't a token...
        new WWWAuthenticateHeader('@');
    }

    public function testInvalidParameterName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "invalid name" for the header "WWW-Authenticate" is not valid'
        );

        // cannot contain spaces...
        new WWWAuthenticateHeader('foo', ['invalid name' => 'value']);
    }

    public function testInvalidParameterNameType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "<integer>" for the header "WWW-Authenticate" is not valid'
        );

        // cannot contain spaces...
        new WWWAuthenticateHeader('foo', [0 => 'value']);
    }

    public function testInvalidParameterValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value ""invalid value"" for the header "WWW-Authenticate" is not valid'
        );

        // cannot contain quotes...
        new WWWAuthenticateHeader('foo', ['name' => '"invalid value"']);
    }

    public function testInvalidParameterValueType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value "<array>" for the header "WWW-Authenticate" is not valid'
        );

        // cannot contain quotes...
        new WWWAuthenticateHeader('foo', ['name' => []]);
    }

    public function testBuild()
    {
        $header = new WWWAuthenticateHeader('foo', ['bar' => 'baz']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new WWWAuthenticateHeader('foo', ['bar' => 'baz']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
