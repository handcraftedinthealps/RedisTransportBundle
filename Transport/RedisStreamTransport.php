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

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport;

use Redis;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class RedisStreamTransport implements TransportInterface
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var RedisStreamReceiver
     */
    private $receiver;

    /**
     * @var RedisStreamSender
     */
    private $sender;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $stream;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $consumer;

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var string|null
     */
    private $auth;

    public function __construct(string $host, int $port, string $stream, string $group = '', string $consumer = '', ?SerializerInterface $serializer = null, ?string $auth)
    {
        $this->host = $host;
        $this->port = $port;
        $this->stream = $stream;
        $this->group = $group;
        $this->consumer = $consumer;
        $this->serializer = $serializer ?? Serializer::create();
        $this->auth = $auth;
    }

    public function receive(callable $handler): void
    {
        ($this->receiver ?? $this->getReceiver())->receive($handler);
    }

    public function stop(): void
    {
        ($this->receiver ?? $this->getReceiver())->stop();
    }

    public function send(Envelope $envelope): Envelope
    {
        return ($this->sender ?? $this->getSender())->send($envelope);
    }

    private function getReceiver(): RedisStreamReceiver
    {
        return $this->receiver = new RedisStreamReceiver(
            $this->redis ?? $this->getRedis(),
            $this->stream,
            $this->group,
            $this->consumer,
            $this->serializer
        );
    }

    private function getSender(): RedisStreamSender
    {
        return $this->sender = new RedisStreamSender($this->redis ?? $this->getRedis(), $this->stream, $this->serializer);
    }

    private function getRedis(): Redis
    {
        $this->redis = new Redis();
        $this->redis->connect($this->host, $this->port);
        if ($this->auth) {
            $this->redis->auth($this->auth);
        }

        return $this->redis;
    }
}
