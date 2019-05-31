<?php

declare(strict_types=1);

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport;

use Symfony\Component\Messenger\Transport\RedisExt\RedisTransportFactory;

class RedisStreamTransportFactory extends RedisTransportFactory
{
    public function supports(string $dsn, array $options): bool
    {
        return 0 === mb_strpos($dsn, 'redis-stream://');
    }
}
