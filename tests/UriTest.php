<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Exception\InvalidUriComponentException;
use Sunrise\Http\Message\Exception\InvalidUriException;
use Sunrise\Http\Message\Uri;

use function strtolower;

class UriTest extends TestCase
{
    private const TEST_URI = 'scheme://user:password@host:3000/path?query#fragment';

    public function testContracts(): void
    {
        $uri = new Uri('/');

        $this->assertInstanceOf(UriInterface::class, $uri);
    }

    // Constructor...

    public function testConstructorWithUri(): void
    {
        $uri = new Uri('/');

        $this->assertSame('/', $uri->__toString());
    }

    public function testConstructorWithoutUri(): void
    {
        $uri = new Uri();

        $this->assertSame('', $uri->__toString());
    }

    public function testConstructorWithEmptyUri(): void
    {
        $uri = new Uri('');

        $this->assertSame('', $uri->__toString());
    }

    public function testConstructorWithInvalidUri(): void
    {
        $this->expectException(InvalidUriException::class);
        $this->expectExceptionMessage('Unable to parse URI');

        new Uri(':');
    }

    public function testCreateWithUri(): void
    {
        $uri = $this->createMock(UriInterface::class);

        $this->assertSame($uri, Uri::create($uri));
    }

    public function testCreateWithStringUri(): void
    {
        $uri = Uri::create(self::TEST_URI);

        $this->assertSame(self::TEST_URI, $uri->__toString());
    }

    public function testCreateWithUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('URI should be a string');

        Uri::create(null);
    }

    // Getters...

    public function testGetScheme(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('scheme', $uri->getScheme());
    }

    public function testGetUserInfo(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('user:password', $uri->getUserInfo());
    }

    public function testGetHost(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('host', $uri->getHost());
    }

    public function testGetPort(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame(3000, $uri->getPort());
    }

    public function testGetPath(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('/path', $uri->getPath());
    }

    public function testGetPathWithoutTwoLeadingSlashes(): void
    {
        $uri = new Uri('//localhost//path');

        $this->assertSame('/path', $uri->getPath());
    }

    public function testGetQuery(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('query', $uri->getQuery());
    }

    public function testGetFragment(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('fragment', $uri->getFragment());
    }

    // Withers...

    public function testWithScheme(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withScheme('new-scheme');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('scheme', $uri->getScheme());
        $this->assertSame('new-scheme', $copy->getScheme());
    }

    public function testWithUserInfo(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withUserInfo('new-user', 'new-password');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('user:password', $uri->getUserInfo());
        $this->assertSame('new-user:new-password', $copy->getUserInfo());
    }

    public function testWithUserInfoWithoutPassword(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withUserInfo('new-user');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('user:password', $uri->getUserInfo());
        $this->assertSame('new-user', $copy->getUserInfo());
    }

    public function testWithHost(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withHost('new-host');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('host', $uri->getHost());
        $this->assertSame('new-host', $copy->getHost());
    }

    public function testWithPort(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withPort(80);

        $this->assertNotSame($uri, $copy);
        $this->assertSame(3000, $uri->getPort());
        $this->assertSame(80, $copy->getPort());
    }

    public function testWithPath(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withPath('/new-path');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('/path', $uri->getPath());
        $this->assertSame('/new-path', $copy->getPath());
    }

    public function testWithQuery(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withQuery('new-query');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('query', $uri->getQuery());
        $this->assertSame('new-query', $copy->getQuery());
    }

    public function testWithFragment(): void
    {
        $uri = new Uri(self::TEST_URI);
        $copy = $uri->withFragment('new-fragment');

        $this->assertNotSame($uri, $copy);
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('new-fragment', $copy->getFragment());
    }

    // Withers with empty data...

    public function testWithEmptyScheme(): void
    {
        $uri = (new Uri(self::TEST_URI))->withScheme('');

        $this->assertSame('', $uri->getScheme());
    }

    public function testWithEmptyUserInfo(): void
    {
        $uri = (new Uri(self::TEST_URI))->withUserInfo('');

        $this->assertSame('', $uri->getUserInfo());
    }

    public function testWithEmptyHost(): void
    {
        $uri = (new Uri(self::TEST_URI))->withHost('');

        $this->assertSame('', $uri->getHost());
    }

    public function testWithEmptyPort(): void
    {
        $uri = (new Uri(self::TEST_URI))->withPort(null);

        $this->assertNull($uri->getPort());
    }

    public function testWithEmptyPath(): void
    {
        $uri = (new Uri(self::TEST_URI))->withPath('');

        $this->assertSame('', $uri->getPath());
    }

    public function testWithEmptyQuery(): void
    {
        $uri = (new Uri(self::TEST_URI))->withQuery('');

        $this->assertSame('', $uri->getQuery());
    }

    public function testWithEmptyFragment(): void
    {
        $uri = (new Uri(self::TEST_URI))->withFragment('');

        $this->assertSame('', $uri->getFragment());
    }

    // Withers with invalid data...

    public function testWithInvalidScheme(): void
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('Invalid URI component "scheme"');

        (new Uri(self::TEST_URI))->withScheme('scheme:');
    }

    public function testWithInvalidUserInfo(): void
    {
        $uri = (new Uri(self::TEST_URI))->withUserInfo('user:password', 'user:password');

        $this->assertSame('user%3Apassword:user%3Apassword', $uri->getUserInfo(), '', 0.0, 10, false, true);
    }

    public function testWithInvalidHost(): void
    {
        $uri = (new Uri(self::TEST_URI))->withHost('host:80');

        // %3A or %3a
        $expected = strtolower('host%3A80');

        $this->assertSame($expected, $uri->getHost(), '', 0.0, 10, false, true);
    }

    public function testWithPortLessThanZero(): void
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('Invalid URI component "port"');

        (new Uri(self::TEST_URI))->withPort(-1);
    }

    public function testWithPortEqualsZero(): void
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('Invalid URI component "port"');

        (new Uri(self::TEST_URI))->withPort(0);
    }

    public function testWithPortGreaterThan65535(): void
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('Invalid URI component "port"');

        (new Uri(self::TEST_URI))->withPort(2 ** 16);
    }

    public function testWithInvalidPath(): void
    {
        $uri = (new Uri(self::TEST_URI))->withPath('/path?query');

        $this->assertSame('/path%3Fquery', $uri->getPath(), '', 0.0, 10, false, true);
    }

    public function testWithInvalidQuery(): void
    {
        $uri = (new Uri(self::TEST_URI))->withQuery('query#fragment');

        $this->assertSame('query%23fragment', $uri->getQuery(), '', 0.0, 10, false, true);
    }

    public function testWithInvalidFragment(): void
    {
        $uri = (new Uri(self::TEST_URI))->withFragment('fragment#fragment');

        $this->assertSame('fragment%23fragment', $uri->getFragment(), '', 0.0, 10, false, true);
    }

    // Withers with invalid data types...

    /**
     * @dataProvider schemeInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForScheme($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "scheme" must be a string');

        (new Uri)->withScheme($value);
    }

    /**
     * @dataProvider userInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForUser($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "user" must be a string');

        (new Uri)->withUserInfo($value);
    }

    /**
     * @dataProvider passwordInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForPass($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "password" must be a string');

        (new Uri)->withUserInfo('user', $value);
    }

    /**
     * @dataProvider hostInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForHost($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "host" must be a string');

        (new Uri)->withHost($value);
    }

    /**
     * @dataProvider portInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForPort($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "port" must be an integer');

        (new Uri)->withPort($value);
    }

    /**
     * @dataProvider pathInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForPath($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "path" must be a string');

        (new Uri)->withPath($value);
    }

    /**
     * @dataProvider queryInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForQuery($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "query" must be a string');

        (new Uri)->withQuery($value);
    }

    /**
     * @dataProvider fragmentInvalidDataTypeProvider
     */
    public function testWithInvalidDataTypeForFragment($value)
    {
        $this->expectException(InvalidUriComponentException::class);
        $this->expectExceptionMessage('URI component "fragment" must be a string');

        (new Uri)->withFragment($value);
    }

    // Builders...

    public function testGetAuthority(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame('user:password@host:3000', $uri->getAuthority());
        $this->assertSame('', $uri->withHost('')->getAuthority());
        $this->assertSame('host:3000', $uri->withUserInfo('')->getAuthority());
        $this->assertSame('user@host:3000', $uri->withUserInfo('user')->getAuthority());
        $this->assertSame('user:password@host', $uri->withPort(null)->getAuthority());
    }

    public function testToString(): void
    {
        $uri = new Uri(self::TEST_URI);

        $this->assertSame(self::TEST_URI, (string) $uri);
    }

    // Normalizes...

    public function testNormalizeScheme(): void
    {
        $uri = new Uri(self::TEST_URI);

        $uri = $uri->withScheme('UPPERCASED-SCHEME');

        $this->assertSame('uppercased-scheme', $uri->getScheme());
    }

    public function testNormalizeHost(): void
    {
        $uri = new Uri(self::TEST_URI);

        $uri = $uri->withHost('UPPERCASED-HOST');

        $this->assertSame('uppercased-host', $uri->getHost());
    }

    // Ignoring the standard ports

    public function testIgnoringStandardPorts(): void
    {
        $uri = new Uri('http://example.com:80/');
        $this->assertNull($uri->getPort());
        $this->assertSame('example.com', $uri->getAuthority());
        $this->assertSame('http://example.com/', (string) $uri);

        $uri = new Uri('https://example.com:443/');
        $this->assertNull($uri->getPort());
        $this->assertSame('example.com', $uri->getAuthority());
        $this->assertSame('https://example.com/', (string) $uri);

        $uri = new Uri('http://example.com:443/');
        $this->assertSame(443, $uri->getPort());
        $this->assertSame('example.com:443', $uri->getAuthority());
        $this->assertSame('http://example.com:443/', (string) $uri);

        $uri = new Uri('https://example.com:80/');
        $this->assertSame(80, $uri->getPort());
        $this->assertSame('example.com:80', $uri->getAuthority());
        $this->assertSame('https://example.com:80/', (string) $uri);
    }

    // Another schemes...

    public function testMailtoScheme(): void
    {
        $uri = new Uri('mailto:test@example.com');

        $this->assertSame('mailto', $uri->getScheme());
        $this->assertSame('test@example.com', $uri->getPath());
    }

    public function testMapsScheme(): void
    {
        $uri = new Uri('maps:?q=112+E+Chapman+Ave+Orange,+CA+92866');

        $this->assertSame('maps', $uri->getScheme());
        $this->assertSame('q=112+E+Chapman+Ave+Orange,+CA+92866', $uri->getQuery());
    }

    public function testTelScheme(): void
    {
        $uri = new Uri('tel:+1-816-555-1212');

        $this->assertSame('tel', $uri->getScheme());
        $this->assertSame('+1-816-555-1212', $uri->getPath());
    }

    public function testUrnScheme(): void
    {
        $uri = new Uri('urn:oasis:names:specification:docbook:dtd:xml:4.1.2');

        $this->assertSame('urn', $uri->getScheme());
        $this->assertSame('oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->getPath());
    }

    // Providers...

    public function schemeInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    public function userInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    public function passwordInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [function () {
            }],
        ];
    }

    public function hostInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    public function portInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            ['a'],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [function () {
            }],
        ];
    }

    public function pathInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    public function queryInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    public function fragmentInvalidDataTypeProvider(): array
    {
        return [
            [true],
            [false],
            [0],
            [0.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    // Issues..

    public function testIssue31(): void
    {
        $uri = new Uri('//username@hostname');
        $uri = $uri->withPath('pathname');
        $this->assertSame('//username@hostname/pathname', $uri->__toString());

        $uri = new Uri('scheme:');
        $uri = $uri->withPath('//pathname');
        $this->assertSame('scheme:/pathname', $uri->__toString());

        $uri = new Uri('scheme:');
        $uri = $uri->withPath('///pathname');
        $this->assertSame('scheme:/pathname', $uri->__toString());
    }
}
