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
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

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

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(Redis $redis, string $stream, string $group = null, string $consumer = null, SerializerInterface $serializer = null)
    {
        $this->redis = $redis;
        $this->stream = $stream;
        $this->group = $group;
        $this->consumer = $consumer;
        $this->serializer = $serializer ?? Serializer::create();
    }

    public function receive(callable $handler): void
    {
        foreach ($this->read() as $key => $message) {
            $content = (array) json_decode($message['content']);

            $content = [
                'body' => $content['body'],
                'headers' => (array) $content['headers'],
            ];

            $handler($this->serializer->decode($content));

            $this->ack($key);
        }
    }

    public function stop(): void
    {
        $this->redis->close();
    }

    private function read()
    {
        // See https://redis.io/topics/streams-intro for special variable description
        //
        // '0' == Will receive all pending messages when using groups
        // '>' == Will receive only new messages when using groups
        // '$' special variable for last id not available in groups use '>' instead
        $lastId = '0';

        if ($this->group) {
            // Receive more messages
            while (true) {
                $messages = $this->redis->xReadGroup($this->group, $this->consumer, [$this->stream => $lastId], 1, 0);

                if (false === $messages) {
                    throw new \RuntimeException($this->redis->getLastError());
                }

                if (0 === count($messages[$this->stream])) {
                    // No pending message wait for new coming messages
                    $lastId = '>';

                    continue;
                }

                foreach ($messages[$this->stream] as $key => $message) {
                    $lastId = $key;

                    yield $key => $message;
                }
            }

            return;
        }

        while (true) {
            // TODO lastId should be read here and saved in `ack` function.
            foreach ($this->redis->xRead([$this->stream => $lastId], 1, 0) as $key => $message) {
                $lastId = $key;
                yield $key => $message;
            }
        }
    }

    private function ack(string $key)
    {
        if ($this->group) {
            $this->redis->xAck($this->stream, $this->group, [$key]);
        }
    }
}
