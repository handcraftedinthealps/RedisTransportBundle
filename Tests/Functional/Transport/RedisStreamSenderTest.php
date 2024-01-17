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

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Tests\Functional\Transport;

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message\DomainEventMessage;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Bridge\Redis\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisReceiver;
use Symfony\Component\Messenger\MessageBusInterface;

class RedisStreamSenderTest extends KernelTestCase
{
    /**
     * @var \Redis
     */
    private $redis;

    public function setUp(): void
    {
        self::bootKernel();

        $this->redis = new \Redis();
        $this->redis->connect(\getenv('REDIS_HOST'), (int) \getenv('REDIS_PORT'));
    }

    public function testSend(): void
    {
        if (\method_exists(self::class, 'getContainer')) {
            /** @var ContainerInterface $container */
            $container = self::getContainer();
        } else {
            /** @var ContainerInterface $container */
            $container = self::$container;
        }

        /** @var MessageBusInterface $messageBus */
        $messageBus = $container->get(MessageBusInterface::class);

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

        if (\class_exists(RedisReceiver::class) && \class_exists(Connection::class)) {
            $redisReceiver = new RedisReceiver(
                Connection::fromDsn(\sprintf(
                    'redis-stream://%s:%s/my_test_stream/my_group?serializer=0',
                    \getenv('REDIS_HOST'),
                    \getenv('REDIS_PORT')
                ), [], $this->redis)
            );
        } else {
            $redisReceiver = new \Symfony\Component\Messenger\Transport\RedisExt\RedisReceiver(
                \Symfony\Component\Messenger\Transport\RedisExt\Connection::fromDsn(\sprintf(
                    'redis-stream://%s:%s/my_test_stream/my_group?serializer=0',
                    \getenv('REDIS_HOST'),
                    \getenv('REDIS_PORT')
                ), [], $this->redis)
            );
        }

        /** @var DomainEventMessage $message */
        $message = $redisReceiver->get()[0]->getMessage();

        $this->assertSame($expectedMessage->getName(), $message->getName());
        $this->assertSame($expectedMessage->getType(), $message->getType());
        $this->assertSame($expectedMessage->getId(), $message->getId());
        $this->assertSame($expectedMessage->getPayload(), $message->getPayload());
        $this->assertSame($expectedMessage->getCreated(), $message->getCreated());
    }

    public function tearDown(): void
    {
        $this->redis->del('my_test_stream');
    }
}
