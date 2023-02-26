### HTTP Headers

#### Access-Control-Allow-Credentials

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Credentials

```php
use Sunrise\Http\Message\Header\AccessControlAllowCredentialsHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AccessControlAllowCredentialsHeader();
$response = $response->withHeader(...$header);
```

#### Access-Control-Allow-Headers

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Headers

```php
use Sunrise\Http\Message\Header\AccessControlAllowHeadersHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AccessControlAllowHeadersHeader('X-Custom-Header', 'Upgrade-Insecure-Requests');
$response = $response->withHeader(...$header);
```

#### Access-Control-Allow-Methods

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Methods

```php
use Sunrise\Http\Message\Header\AccessControlAllowMethodsHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AccessControlAllowMethodsHeader('OPTIONS', 'HEAD', 'GET');
$response = $response->withHeader(...$header);
```

#### Access-Control-Allow-Origin

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin

```php
use Sunrise\Http\Message\Header\AccessControlAllowOriginHeader;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Uri\UriFactory;

$response = (new ResponseFactory)->createResponse();

// A response that tells the browser to allow code from any origin to access
// a resource will include the following:
$header = new AccessControlAllowOriginHeader(null);
$response = $response->withHeader(...$header);

// A response that tells the browser to allow requesting code from the origin
// https://developer.mozilla.org to access a resource will include the following:
$uri = (new UriFactory)->createUri('https://developer.mozilla.org');
$header = new AccessControlAllowOriginHeader($uri);
$response = $response->withHeader(...$header);
```

#### Access-Control-Expose-Headers

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Expose-Headers

```php
use Sunrise\Http\Message\Header\AccessControlExposeHeadersHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AccessControlExposeHeadersHeader('Content-Length', 'X-Kuma-Revision');
$response = $response->withHeader(...$header);
```

#### Access-Control-Max-Age

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Max-Age

```php
use Sunrise\Http\Message\Header\AccessControlMaxAgeHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AccessControlMaxAgeHeader(600);
$response = $response->withHeader(...$header);
```

#### Age

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Age

```php
use Sunrise\Http\Message\Header\AgeHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AgeHeader(24);
$response = $response->withHeader(...$header);
```

#### Allow

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Allow

```php
use Sunrise\Http\Message\Header\AllowHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new AllowHeader('OPTIONS', 'HEAD', 'GET');
$response = $response->withHeader(...$header);
```

#### Cache-Control

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control

```php
use Sunrise\Http\Message\Header\CacheControlHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

// Preventing caching
$header = new CacheControlHeader(['no-cache' => '', 'no-store' => '', 'must-revalidate' => '']);
$response = $response->withHeader(...$header);

// Caching static assets
$header = new CacheControlHeader(['public' => '', 'max-age' => '31536000']);
$response = $response->withHeader(...$header);
```

#### Clear-Site-Data

> Usage link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Clear-Site-Data

```php
use Sunrise\Http\Message\Header\ClearSiteDataHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

// Single directive
$header = new ClearSiteDataHeader(['cache']);
$response = $response->withHeader(...$header);

// Multiple directives (comma separated)
$header = new ClearSiteDataHeader(['cache', 'cookies']);
$response = $response->withHeader(...$header);

// Wild card
$header = new ClearSiteDataHeader(['*']);
$response = $response->withHeader(...$header);
```

#### Content-Disposition

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition

```php
use Sunrise\Http\Message\Header\ContentDispositionHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

// As a response header for the main body
$header = new ContentDispositionHeader('attachment', ['filename' => 'filename.jpg']);
$response = $response->withHeader(...$header);

// As a header for a multipart body
$header = new ContentDispositionHeader('form-data', ['name' => 'fieldName', 'filename' => 'filename.jpg']);
$response = $response->withHeader(...$header);
```

#### Content-Encoding

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding

```php
use Sunrise\Http\Message\Header\ContentEncodingHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ContentEncodingHeader('gzip');
$response = $response->withHeader(...$header);
```

#### Content-Language

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Language

```php
use Sunrise\Http\Message\Header\ContentLanguageHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ContentLanguageHeader('de-DE', 'en-CA');
$response = $response->withHeader(...$header);
```

#### Content-Length

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Length

```php
use Sunrise\Http\Message\Header\ContentLengthHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ContentLengthHeader(4096);
$response = $response->withHeader(...$header);
```

#### Content-Location

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Location

```php
use Sunrise\Http\Message\Header\ContentLocationHeader;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Uri\UriFactory;

$response = (new ResponseFactory)->createResponse();

$uri = (new UriFactory)->createUri('https://example.com/documents/foo');
$header = new ContentLocationHeader($uri);
$response = $response->withHeader(...$header);
```

#### Content-MD5

> Useful link: https://tools.ietf.org/html/rfc1864

```php
use Sunrise\Http\Message\Header\ContentMD5Header;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ContentMD5Header('MzAyMWU2OGRmOWE3MjAwMTM1NzI1YzYzMzEzNjlhMjI=');
$response = $response->withHeader(...$header);
```

#### Content-Range

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Range

```php
use Sunrise\Http\Message\Header\ContentRangeHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ContentRangeHeader(
    200, // An integer in the given unit indicating the beginning of the request range.
    1000, // An integer in the given unit indicating the end of the requested range.
    67589 // The total size of the document.
);
$response = $response->withHeader(...$header);
```

#### Content-Security-Policy

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy

```php
use Sunrise\Http\Message\Header\ContentSecurityPolicyHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

// Pre-existing site that uses too much inline code to fix but wants
// to ensure resources are loaded only over https and disable plugins:
$header = new ContentSecurityPolicyHeader(['default-src' => "https: 'unsafe-eval' 'unsafe-inline'", 'object-src' => "'none'"]);
$response = $response->withAddedHeader(...$header);

// Don't implement the above policy yet; instead just report
// violations that would have occurred:
$header = new ContentSecurityPolicyHeader(['default-src' => 'https:', 'report-uri' => '/csp-violation-report-endpoint/']);
$response = $response->withAddedHeader(...$header);
```

#### Content-Security-Policy-Report-Only

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy-Report-Only

```php
use Sunrise\Http\Message\Header\ContentSecurityPolicyReportOnlyHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

// This header reports violations that would have occurred.
// You can use this to iteratively work on your content security policy.
// You observe how your site behaves, watching for violation reports,
// then choose the desired policy enforced by the Content-Security-Policy header.
$header = new ContentSecurityPolicyReportOnlyHeader(['default-src' => 'https:', 'report-uri' => '/csp-violation-report-endpoint/']);
$response = $response->withAddedHeader(...$header);

// If you still want to receive reporting, but also want
// to enforce a policy, use the Content-Security-Policy header with the report-uri directive.
$header = new ContentSecurityPolicyReportOnlyHeader(['default-src' => 'https:', 'report-uri' => '/csp-violation-report-endpoint/']);
$response = $response->withAddedHeader(...$header);
```

#### Content-Type

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type

```php
use Sunrise\Http\Message\Header\ContentTypeHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ContentTypeHeader('application/json', ['charset' => 'utf-8']);
$response = $response->withHeader(...$header);
```

#### Cookie

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cookie

```php
use Sunrise\Http\Message\Header\CookieHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new CookieHeader(['name' => 'value', 'name2' => 'value2', 'name3' => 'value3']);
$response = $response->withHeader(...$header);
```

#### Date

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Date

```php
use DateTime;
use Sunrise\Http\Message\Header\DateHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new DateHeader(new DateTime('now'));
$response = $response->withHeader(...$header);
```

#### Etag

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag

```php
use Sunrise\Http\Message\Header\EtagHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new EtagHeader('33a64df551425fcc55e4d42a148795d9f25f89d4');
$response = $response->withHeader(...$header);
```

#### Expires

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires

```php
use DateTime;
use Sunrise\Http\Message\Header\ExpiresHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new ExpiresHeader(new DateTime('1 day ago'));
$response = $response->withHeader(...$header);
```

#### Last-Modified

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified

```php
use DateTime;
use Sunrise\Http\Message\Header\LastModifiedHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new LastModifiedHeader(new DateTime('1 year ago'));
$response = $response->withHeader(...$header);
```

#### Link

> Useful link: https://www.w3.org/wiki/LinkHeader

```php
use Sunrise\Http\Message\Header\LinkHeader;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Uri\UriFactory;

$response = (new ResponseFactory)->createResponse();

$uri = (new UriFactory)->createUri('meta.rdf');
$header = new LinkHeader($uri, ['rel' => 'meta']);
$response = $response->withHeader(...$header);
```

#### Location

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Location

```php
use Sunrise\Http\Message\Header\LocationHeader;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Uri\UriFactory;

$response = (new ResponseFactory)->createResponse();

$uri = (new UriFactory)->createUri('/');
$header = new LocationHeader($uri);
$response = $response->withHeader(...$header);
```

#### Refresh

> Useful link: https://en.wikipedia.org/wiki/Meta_refresh

```php
use Sunrise\Http\Message\Header\RefreshHeader;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Uri\UriFactory;

$response = (new ResponseFactory)->createResponse();

$uri = (new UriFactory)->createUri('/login');
$header = new RefreshHeader(3, $uri);
$response = $response->withHeader(...$header);
```

#### Retry-After

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Retry-After

```php
use DateTime;
use Sunrise\Http\Message\Header\RetryAfterHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new RetryAfterHeader(new DateTime('+30 second'));
$response = $response->withHeader(...$header);
```

#### Set-Cookie

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie

```php
use DateTime;
use Sunrise\Http\Message\Header\SetCookieHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

// Session cookie
// Session cookies will get removed when the client is shut down.
// They don't specify the Expires or Max-Age directives.
// Note that web browser have often enabled session restoring.
$header = new SetCookieHeader('sessionid', '38afes7a8', null, ['path' => '/', 'httponly' => true]);
$response = $response->withAddedHeader(...$header);

// Permanent cookie
// Instead of expiring when the client is closed, permanent cookies expire
// at a specific date (Expires) or after a specific length of time (Max-Age).
$header = new SetCookieHeader('id', 'a3fWa', new DateTime('+1 day'), ['secure' => true, 'httponly' => true]);
$response = $response->withAddedHeader(...$header);

// Invalid domains
// A cookie belonging to a domain that does not include the origin server
// should be rejected by the user agent. The following cookie will be rejected
// if it was set by a server hosted on originalcompany.com.
$header = new SetCookieHeader('qwerty', '219ffwef9w0f', new DateTime('+1 day'), ['domain' => 'somecompany.co.uk', 'path' => '/']);
$response = $response->withAddedHeader(...$header);
```

#### Sunset

> Useful link: https://tools.ietf.org/id/draft-wilde-sunset-header-03.html

```php
use DateTime;
use Sunrise\Http\Message\Header\SunsetHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new SunsetHeader(new DateTime('2038-01-19 03:14:07'));
$response = $response->withHeader(...$header);
```

#### Trailer

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Trailer

```php
use Sunrise\Http\Message\Header\TrailerHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new TrailerHeader('Expires', 'X-Streaming-Error');
$response = $response->withHeader(...$header);
```

#### Transfer-Encoding

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Transfer-Encoding

```php
use Sunrise\Http\Message\Header\TransferEncodingHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new TransferEncodingHeader('gzip', 'chunked');
$response = $response->withHeader(...$header);
```

#### Vary

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Vary

```php
use Sunrise\Http\Message\Header\VaryHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new VaryHeader('User-Agent', 'Content-Language');
$response = $response->withHeader(...$header);
```

#### WWW-Authenticate

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/WWW-Authenticate

```php
use Sunrise\Http\Message\Header\WWWAuthenticateHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new WWWAuthenticateHeader(WWWAuthenticateHeader::HTTP_AUTHENTICATE_SCHEME_BASIC, ['realm' => 'Access to the staging site', 'charset' => 'UTF-8']);
$response = $response->withHeader(...$header);
```

#### Warning

> Useful link: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Warning

```php
use DateTime;
use Sunrise\Http\Message\Header\WarningHeader;
use Sunrise\Http\Message\ResponseFactory;

$response = (new ResponseFactory)->createResponse();

$header = new WarningHeader(WarningHeader::HTTP_WARNING_CODE_RESPONSE_IS_STALE, 'anderson/1.3.37', 'Response is stale', new DateTime('now'));
$response = $response->withHeader(...$header);
```
