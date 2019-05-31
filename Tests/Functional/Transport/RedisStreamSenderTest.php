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

namespace Intosite\Bundle\LocationProjectionBundle\Tests\Functional\Transport;

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message\DomainEventMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\RedisExt\Connection;
use Symfony\Component\Messenger\Transport\RedisExt\RedisReceiver;

class RedisStreamSenderTest extends KernelTestCase
{
    /**
     * @var \Redis
     */
    private $redis;

    public function setUp()
    {
        self::bootKernel();

        $this->redis = new \Redis();
        $this->redis->connect(getenv('REDIS_HOST'), (int) getenv('REDIS_PORT'));
    }

    public function testSend()
    {
        $container = self::$container;

        /** @var MessageBusInterface $messageBus */
        $messageBus = $container->get('message_bus');

        $expectedMessage = new DomainEventMessage(
            'mountain.created',
            'mountain',
            '1',
            [
                'id' => '1',
                'name' => 'Piz Buin',
                'height' => 3312,
            ]
        );

        $messageBus->dispatch($expectedMessage);

        $redisReceiver = new RedisReceiver(
            Connection::fromDsn('redis-stream://127.0.0.1:6379/my_test_stream', [], $this->redis)
        );

        /** @var DomainEventMessage $message */
        $message = $redisReceiver->get()[0]->getMessage();

        $this->assertSame($expectedMessage->getName(), $message->getName());
        $this->assertSame($expectedMessage->getType(), $message->getType());
        $this->assertSame($expectedMessage->getId(), $message->getId());
        $this->assertSame($expectedMessage->getPayload(), $message->getPayload());
        $this->assertSame($expectedMessage->getCreated(), $message->getCreated());
    }

    public function tearDown()
    {
        $this->redis->del('my_test_stream');
    }
}
