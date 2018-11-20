<?php

declare(strict_types=1);

/*
 * This file is part of Handcrafted in the Alps - Redis Transport Bundle Project.
 *
 * (c) Sulu GmbH <hello@sulu.io>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Transport;

use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class RedisStreamTransportFactory implements TransportFactoryInterface
{
    public function createTransport(string $dsn, array $options): TransportInterface
    {
        $parsedUrl = parse_url($dsn);

        if (false === $parsedUrl) {
            throw new \InvalidArgumentException(
                sprintf('The given dsn("%s") for the redis connection was not valid.', $dsn)
            );
        }

        return new RedisStreamTransport($parsedUrl['host'], $parsedUrl['port'], ltrim($parsedUrl['path'], '/'));
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === mb_strpos($dsn, 'redis-stream://');
    }
}
