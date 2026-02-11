<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Stream;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\Stream\FileStream;

final class FileStreamTest extends TestCase
{
    public function testFilename(): void
    {
        $stream = new FileStream(__FILE__, 'r');
        self::assertSame(__FILE__, $stream->getFilename());
    }
}
