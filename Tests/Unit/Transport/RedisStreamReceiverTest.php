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

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Tests\Unit\Transport;

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message\DomainEventMessage;
use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport\RedisStreamReceiver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RedisStreamReceiverTest extends TestCase
{
    /**
     * @var RedisStreamReceiver
     */
    private $redisStreamReceiver;

    /**
     * @var \Redis
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->prophesize(\Redis::class);
        $this->redisStreamReceiver = new RedisStreamReceiver(
            $this->redis->reveal(),
            'test_stream',
            'test_group',
            'test_consumer'
        );
    }

    public function testReceive()
    {
        // xreadgroup instead of xReadGroup because of: https://github.com/phpredis/phpredis/issues/1489
        $this->redis->xreadgroup('test_group', 'test_consumer', ['test_stream' => '0'], 1, Argument::any())->willReturn(
            [
                'test_stream' => [
                    'test_id' => [
                        'content' => json_encode([
                            'body' => json_encode([
                                'name' => 'mountain.created',
                                'type' => 'mountain',
                                'id' => '1',
                                'payload' => [
                                    'id' => 1,
                                    'name' => 'Piz Buin',
                                    'height' => 3312,
                                ],
                                'created' => date('c'),
                            ]),
                            'headers' => [
                                'type' => DomainEventMessage::class,
                            ],
                        ]),
                    ],
                ],
            ]
        )->shouldBeCalled();

        // xack instead of xAck because of: https://github.com/phpredis/phpredis/issues/1489
        $this->redis->xack('test_stream', 'test_group', ['test_id'])->shouldBeCalled();
        $this->redisStreamReceiver->receive(function () {
            $this->assertTrue(true);

            $this->redisStreamReceiver->stop();
        });
    }
}
