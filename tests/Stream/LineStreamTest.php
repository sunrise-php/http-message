<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Stream;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\Stream\LineStream;

use function fopen;
use function implode;

use const PHP_EOL;

final class LineStreamTest extends TestCase
{
    public function testIterator(): void
    {
        $stream = new LineStream(fopen('php://memory', 'r+'));
        $stream->write(implode(PHP_EOL, ['foo', 'bar', 'baz']));
        $stream->rewind();
        self::assertSame(['foo', 'bar', 'baz'], [...$stream]);
    }
}
