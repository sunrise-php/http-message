<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Integration;

use Http\Psr7Test\StreamIntegrationTest as BaseStreamIntegrationTest;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Stream\PhpTempStream;
use Sunrise\Http\Message\Stream;

use function is_string;

class StreamIntegrationTest extends BaseStreamIntegrationTest
{

    /**
     * {@inheritdoc}
     */
    public function createStream($data): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        if (is_string($data)) {
            return new PhpTempStream($data);
        }

        return new Stream($data);
    }
}
