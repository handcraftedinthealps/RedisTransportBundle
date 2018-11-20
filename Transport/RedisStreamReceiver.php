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

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle;

use Redis;
use Symfony\Component\Messenger\Transport\ReceiverInterface;

class RedisStreamReceiver implements ReceiverInterface
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $streamName;

    /**
     * @var string
     */
    protected $group;

    /**
     * @var string
     */
    protected $consumer;

    public function __construct(Redis $redis, string $streamName, string $group = null, string $consumer = null)
    {
        $this->redis = $redis;
        $this->streamName = $streamName;
        $this->group = $group;
        $this->consumer = $consumer;
    }

    public function receive(callable $handler): void
    {
        while ($message = $this->read()) {
            // TODO read
        }
    }

    public function stop(): void
    {
        $this->redis->close();
    }

    private function read()
    {
        if ($this->group) {
            return $this->redis->xReadGroup($this->group, $this->consumer, [$this->streamName => 0], 1);
        }

        return $this->redis->xRead([$this->streamName => 0], 1);
    }
}
