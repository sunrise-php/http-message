<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\SetCookieHeader;

class SetCookieHeaderTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame('Lax', SetCookieHeader::SAME_SITE_LAX);
        $this->assertSame('Strict', SetCookieHeader::SAME_SITE_STRICT);
        $this->assertSame('None', SetCookieHeader::SAME_SITE_NONE);
    }

    public function testContracts()
    {
        $header = new SetCookieHeader('name', 'value');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new SetCookieHeader('name', 'value');

        $this->assertSame('Set-Cookie', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new SetCookieHeader('name', 'value');

        $this->assertSame('name=value; Path=/; HttpOnly; SameSite=Lax', $header->getFieldValue());
    }

    public function testEncodingName()
    {
        $header = new SetCookieHeader('@foo', 'bar');

        $this->assertSame('%40foo=bar; Path=/; HttpOnly; SameSite=Lax', $header->getFieldValue());
    }

    public function testEncodingValue()
    {
        $header = new SetCookieHeader('foo', '@bar');

        $this->assertSame('foo=%40bar; Path=/; HttpOnly; SameSite=Lax', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $dt = new \DateTime('-1 year', new \DateTimeZone('UTC'));
        $header = new SetCookieHeader('name', '');

        $expected = \sprintf(
            'name=deleted; Expires=%s; Max-Age=0; Path=/; HttpOnly; SameSite=Lax',
            $dt->format(\DateTime::RFC822)
        );

        $this->assertSame($expected, $header->getFieldValue());
    }

    public function testExpiresWithMutableDateTime()
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $utc = new \DateTime('now', new \DateTimeZone('UTC'));

        $header = new SetCookieHeader('name', 'value', $now);

        $expected = \sprintf(
            'name=value; Expires=%s; Max-Age=0; Path=/; HttpOnly; SameSite=Lax',
            $utc->format(\DateTime::RFC822)
        );

        $this->assertSame($expected, $header->getFieldValue());
        $this->assertSame('Europe/Moscow', $now->getTimezone()->getName());
    }

    public function testExpiresWithImmutableDateTime()
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));
        $utc = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $header = new SetCookieHeader('name', 'value', $now);

        $expected = \sprintf(
            'name=value; Expires=%s; Max-Age=0; Path=/; HttpOnly; SameSite=Lax',
            $utc->format(\DateTimeImmutable::RFC822)
        );

        $this->assertSame($expected, $header->getFieldValue());
        $this->assertSame('Europe/Moscow', $now->getTimezone()->getName());
    }

    public function testNegativeExpires()
    {
        $utc = new \DateTime('-30 seconds', new \DateTimeZone('UTC'));
        $header = new SetCookieHeader('name', 'value', $utc);

        // the max-age attribute cannot be negative...
        $expected = \sprintf(
            'name=value; Expires=%s; Max-Age=0; Path=/; HttpOnly; SameSite=Lax',
            $utc->format(\DateTime::RFC822)
        );

        $this->assertSame($expected, $header->getFieldValue());
    }

    public function testPositiveExpires()
    {
        $utc = new \DateTime('+30 seconds', new \DateTimeZone('UTC'));
        $header = new SetCookieHeader('name', 'value', $utc);

        $expected = \sprintf(
            'name=value; Expires=%s; Max-Age=30; Path=/; HttpOnly; SameSite=Lax',
            $utc->format(\DateTime::RFC822)
        );

        $this->assertSame($expected, $header->getFieldValue());
    }

    public function testPath()
    {
        $header = new SetCookieHeader('name', 'value', null, [
            'path' => '/assets/',
        ]);

        $this->assertSame('name=value; Path=/assets/; HttpOnly; SameSite=Lax', $header->getFieldValue());
    }

    public function testDomain()
    {
        $header = new SetCookieHeader('name', 'value', null, [
            'domain' => 'acme.com',
        ]);

        $this->assertSame('name=value; Path=/; Domain=acme.com; HttpOnly; SameSite=Lax', $header->getFieldValue());
    }

    public function testSecure()
    {
        $header = new SetCookieHeader('name', 'value', null, [
            'secure' => true,
        ]);

        $this->assertSame('name=value; Path=/; Secure; HttpOnly; SameSite=Lax', $header->getFieldValue());
    }

    public function testHttpOnly()
    {
        $header = new SetCookieHeader('name', 'value', null, [
            'httpOnly' => false,
        ]);

        $this->assertSame('name=value; Path=/; SameSite=Lax', $header->getFieldValue());
    }

    public function testSameSite()
    {
        $header = new SetCookieHeader('name', 'value', null, [
            'sameSite' => 'Strict',
        ]);

        $this->assertSame('name=value; Path=/; HttpOnly; SameSite=Strict', $header->getFieldValue());
    }

    public function testEmptyName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cookie name cannot be empty');

        new SetCookieHeader('', 'value');
    }

    public function testInvalidName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie name "name=" contains prohibited characters');

        new SetCookieHeader('name=', 'value');
    }

    public function testInvalidPath()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie option "path" contains prohibited characters');

        new SetCookieHeader('name', 'value', null, ['path' => ';']);
    }

    public function testInvalidPathDataType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie option "path" must be a string');

        new SetCookieHeader('name', 'value', null, ['path' => []]);
    }

    public function testInvalidDomain()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie option "domain" contains prohibited characters');

        new SetCookieHeader('name', 'value', null, ['domain' => ';']);
    }

    public function testInvalidDomainDataType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie option "domain" must be a string');

        new SetCookieHeader('name', 'value', null, ['domain' => []]);
    }

    public function testInvalidSamesite()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie option "sameSite" contains prohibited characters');

        new SetCookieHeader('name', 'value', null, ['sameSite' => ';']);
    }

    public function testInvalidSamesiteDataType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cookie option "sameSite" must be a string');

        new SetCookieHeader('name', 'value', null, ['sameSite' => []]);
    }

    public function testBuild()
    {
        $header = new SetCookieHeader('name', 'value');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new SetCookieHeader('name', 'value');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
