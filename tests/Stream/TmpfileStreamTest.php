<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Stream;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\Stream\TmpfileStream;

final class TmpfileStreamTest extends TestCase
{
    public function testFilename(): void
    {
        $stream = new TmpfileStream();
        self::assertFileExists($stream->getFilename());
    }
}
