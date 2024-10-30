<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Stream\TmpfileStream;
use Sunrise\Http\Message\ServerRequestFactory;
use Sunrise\Http\Message\Uri;

use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_NO_FILE;

class ServerRequestFactoryTest extends TestCase
{
    public function testContracts(): void
    {
        $factory = new ServerRequestFactory();

        $this->assertInstanceOf(ServerRequestFactoryInterface::class, $factory);
    }

    public function testCreateServerRequest(): void
    {
        $uri = new Uri();

        $request = (new ServerRequestFactory)->createServerRequest('POST', $uri);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame($uri, $request->getUri());
    }

    public function testCreateServerRequestWithLowerCaseMethod(): void
    {
        $this->assertSame('post', (new ServerRequestFactory)
            ->createServerRequest('post', new Uri())
            ->getMethod());
    }

    public function testCreateServerRequestWithNonStandardMethod(): void
    {
        $this->assertSame('CUSTOM', (new ServerRequestFactory)
            ->createServerRequest('CUSTOM', new Uri())
            ->getMethod());
    }

    public function testCreateServerRequestWithEmptyMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method cannot be an empty');

        (new ServerRequestFactory)->createServerRequest('', new Uri());
    }

    public function testCreateServerRequestWithInvalidMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method');

        (new ServerRequestFactory)->createServerRequest("GET\0", new Uri());
    }

    public function testCreateServerRequestWithStringUri(): void
    {
        $this->assertSame('/foo', (new ServerRequestFactory)
            ->createServerRequest('GET', '/foo')
            ->getUri()
            ->__toString());
    }

    public function testCreateServerRequestWithInvalidUri(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URI');

        (new ServerRequestFactory)->createServerRequest('GET', ':');
    }

    public function testCreateServerRequestWithServerParams(): void
    {
        $this->assertSame(['foo' => 'bar'], (new ServerRequestFactory)
            ->createServerRequest('GET', new Uri(), ['foo' => 'bar'])
            ->getServerParams());
    }

    /**
     * @dataProvider serverParamsWithProtocolVersionProvider
     */
    public function testCreateServerRequestWithServerParamsWithProtocolVersion(
        array $serverParams,
        string $expectedProtocolVersion
    ): void {
        $this->assertSame($expectedProtocolVersion, (new ServerRequestFactory)
            ->createServerRequest('GET', new Uri(), $serverParams)
            ->getProtocolVersion());
    }

    public function testCreateServerRequestWithServerParamsWithUnsupportedProtocolVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unallowed HTTP version');

        (new ServerRequestFactory)->createServerRequest('GET', new Uri(), ['SERVER_PROTOCOL' => 'HTTP/3']);
    }

    /**
     * @dataProvider serverParamsProviderWithHeaders
     */
    public function testCreateServerRequestWithServerParamsWithHeaders(
        array $serverParams,
        array $expectedHeaders
    ): void {
        $this->assertSame($expectedHeaders, (new ServerRequestFactory)
            ->createServerRequest('GET', new Uri(), $serverParams)
            ->getHeaders());
    }

    public function testCreateServerRequestWithServerParamsWithInvalidHeader(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header "X-Foo[0]" is invalid');

        (new ServerRequestFactory)->createServerRequest('GET', new Uri(), ['HTTP_X_FOO' => "\0"]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithServerParams(): void
    {
        $_SERVER = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals();
        $this->assertSame($_SERVER, $request->getServerParams());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithQueryParams(): void
    {
        $_GET = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals();
        $this->assertSame($_GET, $request->getQueryParams());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithCookieParams(): void
    {
        $_COOKIE = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals();
        $this->assertSame($_COOKIE, $request->getCookieParams());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithParsedBody(): void
    {
        $_POST = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals();
        $this->assertSame($_POST, $request->getParsedBody());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     * @dataProvider serverParamsWithProtocolVersionProvider
     */
    public function testCreateServerRequestFromGlobalsWithProtocolVersion(
        array $serverParams,
        string $expectedProtocolVersion
    ): void {
        $_SERVER = $serverParams;
        $request = ServerRequestFactory::fromGlobals();

        $this->assertSame($serverParams, $request->getServerParams());
        $this->assertSame($expectedProtocolVersion, $request->getProtocolVersion());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithUnsupportedProtocolVersion(): void
    {
        $_SERVER = ['SERVER_PROTOCOL' => 'HTTP/3'];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unallowed HTTP version');

        ServerRequestFactory::fromGlobals();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     * @dataProvider serverParamsWithMethodProvider
     */
    public function testCreateServerRequestFromGlobalsWithMethod(
        array $serverParams,
        string $expectedMethod
    ): void {
        $_SERVER =  $serverParams;
        $request = ServerRequestFactory::fromGlobals();

        $this->assertSame($serverParams, $request->getServerParams());
        $this->assertSame($expectedMethod, $request->getMethod());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithEmptyMethod(): void
    {
        $_SERVER = ['REQUEST_METHOD' => ''];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method cannot be an empty');

        ServerRequestFactory::fromGlobals();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithInvalidMethod(): void
    {
        $_SERVER = ['REQUEST_METHOD' => "GET\0"];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method');

        ServerRequestFactory::fromGlobals();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     * @dataProvider serverParamsWithUriProvider
     */
    public function testCreateServerRequestFromGlobalsWithUri(array $serverParams, string $expectedUri): void
    {
        $_SERVER = $serverParams;
        $request = ServerRequestFactory::fromGlobals();

        $this->assertSame($serverParams, $request->getServerParams());
        $this->assertSame($expectedUri, $request->getUri()->__toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithInvalidUri(): void
    {
        $_SERVER = ['HTTP_HOST' => 'localhost:65536'];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URI');

        ServerRequestFactory::fromGlobals();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     * @dataProvider serverParamsProviderWithHeaders
     */
    public function testCreateServerRequestFromGlobalsWithHeaders(array $serverParams, array $expectedHeaders): void
    {
        $expectedHeaders = ['Host' => ['localhost']] + $expectedHeaders;

        $_SERVER = $serverParams;
        $request = ServerRequestFactory::fromGlobals();

        $this->assertSame($serverParams, $request->getServerParams());
        $this->assertSame($expectedHeaders, $request->getHeaders());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithInvalidHeader(): void
    {
        $_SERVER = ['HTTP_X_FOO' => "\0"];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of the HTTP header "X-Foo[0]" is invalid');

        ServerRequestFactory::fromGlobals();
    }

    public function testCreateServerRequestFromGlobalsWithCustomServerParams(): void
    {
        $serverParams = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals($serverParams);
        $this->assertSame($serverParams, $request->getServerParams());
    }

    public function testCreateServerRequestFromGlobalsWithCustomQueryParams(): void
    {
        $queryParams = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals(null, $queryParams);
        $this->assertSame($queryParams, $request->getQueryParams());
    }

    public function testCreateServerRequestFromGlobalsWithCustomCookieParams(): void
    {
        $cookieParams = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals(null, null, $cookieParams);
        $this->assertSame($cookieParams, $request->getCookieParams());
    }

    public function testCreateServerRequestFromGlobalsWithCustomParsedBody(): void
    {
        $parsedBody = ['foo' => 'bar'];
        $request = ServerRequestFactory::fromGlobals(null, null, null, null, $parsedBody);
        $this->assertSame($parsedBody, $request->getParsedBody());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState
     */
    public function testCreateServerRequestFromGlobalsWithUploadedFiles(): void
    {
        $tmpfile = new TmpfileStream();
        $filename = $tmpfile->getMetadata('uri');

        $_FILES['foo']['tmp_name'] = $filename;
        $_FILES['foo']['size'] = 42;
        $_FILES['foo']['error'] = UPLOAD_ERR_OK;
        $_FILES['foo']['name'] = 'foo.txt';
        $_FILES['foo']['type'] = 'text/foo';

        $_FILES['bar']['tmp_name'][0] = $filename;
        $_FILES['bar']['size'][0] = 42;
        $_FILES['bar']['error'][0] = UPLOAD_ERR_OK;
        $_FILES['bar']['name'][0] = 'bar.txt';
        $_FILES['bar']['type'][0] = 'text/bar';

        // must be ignored
        $_FILES['baz']['tmp_name'] = $filename;
        $_FILES['baz']['size'] = 0;
        $_FILES['baz']['error'] = UPLOAD_ERR_NO_FILE;
        $_FILES['baz']['name'] = 'baz.txt';
        $_FILES['baz']['type'] = 'text/baz';

        $uploadedFiles = ServerRequestFactory::fromGlobals()->getUploadedFiles();

        $this->assertArrayHasKey('foo', $uploadedFiles);
        $this->assertSame($_FILES['foo']['tmp_name'], $uploadedFiles['foo']->getStream()->getMetadata('uri'));
        $this->assertSame($_FILES['foo']['size'], $uploadedFiles['foo']->getSize());
        $this->assertSame($_FILES['foo']['error'], $uploadedFiles['foo']->getError());
        $this->assertSame($_FILES['foo']['name'], $uploadedFiles['foo']->getClientFilename());
        $this->assertSame($_FILES['foo']['type'], $uploadedFiles['foo']->getClientMediaType());

        $this->assertArrayHasKey('bar', $uploadedFiles);
        $this->assertArrayHasKey(0, $uploadedFiles['bar']);
        $this->assertSame($_FILES['bar']['tmp_name'][0], $uploadedFiles['bar'][0]->getStream()->getMetadata('uri'));
        $this->assertSame($_FILES['bar']['size'][0], $uploadedFiles['bar'][0]->getSize());
        $this->assertSame($_FILES['bar']['error'][0], $uploadedFiles['bar'][0]->getError());
        $this->assertSame($_FILES['bar']['name'][0], $uploadedFiles['bar'][0]->getClientFilename());
        $this->assertSame($_FILES['bar']['type'][0], $uploadedFiles['bar'][0]->getClientMediaType());

        $this->assertArrayNotHasKey('baz', $uploadedFiles);
    }

    public function testCreateServerRequestFromGlobalsWithCustomUploadedFiles(): void
    {
        $tmpfile = new TmpfileStream();
        $filename = $tmpfile->getMetadata('uri');

        $files = [
            'foo' => [
                'tmp_name' => $filename,
                'size' => 42,
                'error' => UPLOAD_ERR_OK,
                'name' => 'foo.txt',
                'type' => 'text/foo',
            ],
        ];

        $uploadedFiles = ServerRequestFactory::fromGlobals(null, null, null, $files)->getUploadedFiles();

        $this->assertArrayHasKey('foo', $uploadedFiles);
        $this->assertSame($files['foo']['tmp_name'], $uploadedFiles['foo']->getStream()->getMetadata('uri'));
        $this->assertSame($files['foo']['size'], $uploadedFiles['foo']->getSize());
        $this->assertSame($files['foo']['error'], $uploadedFiles['foo']->getError());
        $this->assertSame($files['foo']['name'], $uploadedFiles['foo']->getClientFilename());
        $this->assertSame($files['foo']['type'], $uploadedFiles['foo']->getClientMediaType());
    }

    public function serverParamsWithProtocolVersionProvider(): array
    {
        return [
            [
                ['SERVER_PROTOCOL' => 'HTTP/1.0'],
                '1.0',
            ],
            [
                ['SERVER_PROTOCOL' => 'HTTP/1.1'],
                '1.1',
            ],
            [
                ['SERVER_PROTOCOL' => 'HTTP/2.0'],
                '2.0',
            ],
            [
                ['SERVER_PROTOCOL' => 'HTTP/2'],
                '2',
            ],
            [
                ['SERVER_PROTOCOL' => 'oO'],
                '1.1',
            ],
        ];
    }

    public function serverParamsWithMethodProvider(): array
    {
        return [
            [
                ['REQUEST_METHOD' => 'POST'],
                'POST',
            ],
            [
                ['REQUEST_METHOD' => 'post'],
                'post',
            ],
            [
                ['REQUEST_METHOD' => 'CUSTOM'],
                'CUSTOM',
            ],
        ];
    }

    public function serverParamsWithUriProvider(): array
    {
        return [
            [
                [
                ],
                'http://localhost/',
            ],
            [
                [
                    'HTTPS' => 'off',
                ],
                'http://localhost/',
            ],
            [
                [
                    'HTTPS' => 'on',
                ],
                'https://localhost/',
            ],
            [
                [
                    'HTTP_HOST' => 'example.com',
                ],
                'http://example.com/',
            ],
            [
                [
                    'HTTP_HOST' => 'example.com:3000',
                ],
                'http://example.com:3000/',
            ],
            [
                [
                    'SERVER_NAME' => 'example.com',
                ],
                'http://example.com/',
            ],
            [
                [
                    'SERVER_NAME' => 'example.com',
                    'SERVER_PORT' => 3000,
                ],
                'http://example.com:3000/',
            ],
            [
                [
                    'SERVER_PORT' => 3000,
                ],
                'http://localhost/',
            ],
            [
                [
                    'REQUEST_URI' => '/path',
                ],
                'http://localhost/path',
            ],
            [
                [
                    'REQUEST_URI' => '/path?query',
                ],
                'http://localhost/path?query',
            ],
            [
                [
                    'PHP_SELF' => '/path',
                ],
                'http://localhost/path',
            ],
            [
                [
                    'PHP_SELF' => '/path',
                    'QUERY_STRING' => 'query',
                ],
                'http://localhost/path?query',
            ],
            [
                [
                    'QUERY_STRING' => 'query',
                ],
                'http://localhost/',
            ],
        ];
    }

    public function serverParamsProviderWithHeaders(): array
    {
        return [
            [
                [
                    'HTTP_X_FOO' => 'bar',
                ],
                [
                    'X-Foo' => ['bar'],
                ],
            ],
            [
                [
                    'CONTENT_LENGTH' => '100',
                ],
                [
                    'Content-Length' => ['100'],
                ],
            ],
            [
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                [
                    'Content-Type' => ['application/json'],
                ],
            ],
            [
                [
                    'NON_HEADER_HTTP_TEST' => '',
                ],
                [
                ],
            ],
        ];
    }
}
