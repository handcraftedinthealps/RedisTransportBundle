<?php

declare(strict_types = 1);

/*
 * This file is part of Handcrafted in the Alps - Redis Transport Bundle Project.
 *
 * (c) Sulu GmbH <hello@sulu.io>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport;

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
    protected $stream;

    /**
     * @var string
     */
    protected $group;

    /**
     * @var string
     */
    protected $consumer;

    public function __construct(Redis $redis, string $stream, string $group = null, string $consumer = null)
    {
        $this->redis = $redis;
        $this->stream = $stream;
        $this->group = $group;
        $this->consumer = $consumer;
    }

    public function receive(callable $handler): void
    {
        foreach ($this->read() as $message) {
            // TODO receive message

            $this->ack($message);
        }
    }

    public function stop(): void
    {
        $this->redis->close();
    }

    private function read()
    {
        if ($this->group) {
            // First check if the consumer has pending elements.
            $pendingIds = $this->redis->xPending($this->stream, $this->group, '-', '+', 1, $this->consumer);

            if (false === $pendingIds) {
                throw new \RuntimeException($this->redis->getLastError());
            }

            foreach ($pendingIds as $pendingId) {
                yield reset($this->redis->xRange($this->stream, $pendingId, $pendingId));
            }

            // Receive more messages
            while (true) {
                var_dump('test#);');
                var_dump($this->redis->xReadGroup($this->group, $this->consumer, [$this->stream => '0'], 1));
                var_dump($this->redis->getLastError());
                exit;

                // TODO get last message id instead of using 0
                yield $this->redis->xReadGroup($this->group, $this->consumer, [$this->stream => 0], 1);
            }
        }

        while (true) {
            var_dump($this->redis->xRead([$this->stream => 0], 1, 10));
            exit;

            // TODO get last message id instead of using 0
            yield reset($this->redis->xRead([$this->stream => 0], 1));
        }
    }

    private function ack(array $message)
    {
        if ($this->group) {
            $this->redis->xAck($this->stream, $this->group, [$message['id']]);
        }
    }
}
