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
use Symfony\Component\Messenger\Transport\SenderInterface;

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

    public function __construct(Redis $redis, string $stream)
    {
        $this->redis = $redis;
        $this->stream = $stream;
    }

    public function send(Envelope $envelope)
    {
        $message = $envelope->getMessage();

        // TODO implement serialization

        $this->redis->xAdd($this->stream, '*', $message);
    }
}
