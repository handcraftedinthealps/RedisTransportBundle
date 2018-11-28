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
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class RedisStreamSender implements SenderInterface
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
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(Redis $redis, string $stream, SerializerInterface $serializer = null)
    {
        $this->redis = $redis;
        $this->stream = $stream;
        $this->serializer = $serializer ?? Serializer::create();
    }

    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);

        $this->redis->xAdd($this->stream, '*', $encodedMessage);

        return $envelope;
    }
}
