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

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\RedisStreamReceiver;
use HandcraftedInTheAlps\Bundle\RedisTransportBundle\RedisStreamSender;
use Redis;
use Symfony\Component\Messenger\Envelope;
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
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $stream;

    public function __construct($host, $port, $stream)
    {
        $this->host = $host;
        $this->port = $port;
        $this->stream = $stream;
    }

    public function receive(callable $handler): void
    {
        ($this->receiver ?? $this->getReceiver())->receive($handler);
    }

    public function stop(): void
    {
        ($this->receiver ?? $this->getReceiver())->stop();
    }

    public function send(Envelope $envelope)
    {
        return ($this->sender ?? $this->getSender())->send($envelope);
    }

    private function getReceiver(): RedisStreamReceiver
    {
        return $this->receiver = new RedisStreamReceiver($this->redis ?? $this->getRedis(), $this->stream);
    }

    private function getSender(): RedisStreamSender
    {
        return $this->sender = new RedisStreamSender($this->redis ?? $this->getRedis(), $this->stream);
    }

    private function getRedis(): Redis
    {
        ini_set('default_socket_timeout', -1);
        $this->redis = new Redis();
        $this->redis->connect($this->host, $this->port);

        return $this->redis;
    }
}
