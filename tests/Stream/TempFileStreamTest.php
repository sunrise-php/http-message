<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Stream;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\Stream\TempFileStream;

final class TempFileStreamTest extends TestCase
{
    public function testFilename() : void
    {
        $stream = new TempFileStream();
        self::assertFileExists($stream->getFilename());
    }
}
