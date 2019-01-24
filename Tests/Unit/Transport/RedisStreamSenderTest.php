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

namespace Intosite\Bundle\LocationProjectionBundle\Tests\Unit\Transport;

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message\DomainEventMessage;
use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport\RedisStreamSender;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;

class RedisStreamSenderTest extends TestCase
{
    /**
     * @var RedisStreamSender
     */
    private $redisStreamSender;

    /**
     * @var \Redis
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->prophesize(\Redis::class);
        $this->redisStreamSender = new RedisStreamSender($this->redis->reveal(), 'test_stream');
    }

    public function testSend()
    {
        $domainMessage = new DomainEventMessage(
            'mountain.created',
            'mountain',
            '1',
            [
                'id' => 1,
                'name' => 'Piz Buin',
                'height' => 3312,
            ]
        );

        $envelop = new Envelope($domainMessage);

        // xadd instead of xAdd because of: https://github.com/phpredis/phpredis/issues/1489
        $this->redis->xadd('test_stream', '*', ['content' => json_encode([
            'body' => json_encode([
                'name' => 'mountain.created',
                'type' => 'mountain',
                'id' => '1',
                'payload' => [
                    'id' => 1,
                    'name' => 'Piz Buin',
                    'height' => 3312,
                ],
                'created' => $domainMessage->getCreated(),
            ]),
            'headers' => [
                'type' => DomainEventMessage::class,
            ],
        ])])->willReturn('id')->shouldBeCalled();

        $this->redisStreamSender->send($envelop);
    }
}
