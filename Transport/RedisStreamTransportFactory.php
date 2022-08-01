<?php

declare(strict_types=1);

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport;

use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisTransportFactory;

if (\class_exists(RedisTransportFactory::class)) {
    class RedisStreamTransportFactory extends RedisTransportFactory
    {
        /**
         * @param mixed[] $options
         */
        public function supports(string $dsn, array $options): bool
        {
            return 0 === \mb_strpos($dsn, 'redis-stream://');
        }
    }
} else {
    class RedisStreamTransportFactory extends \Symfony\Component\Messenger\Transport\RedisExt\RedisTransportFactory
    {
        /**
         * @param mixed[] $options
         */
        public function supports(string $dsn, array $options): bool
        {
            return 0 === \mb_strpos($dsn, 'redis-stream://');
        }
    }
}
