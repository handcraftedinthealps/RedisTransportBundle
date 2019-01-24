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

        $message = new DomainEventMessage(
            'mountain.created',
            'mountain',
            '1',
            [
                'id' => '1',
                'name' => 'Piz Buin',
                'height' => 3312,
            ]
        );

        $messageBus->dispatch($message);

        $messages = $this->redis->xRevRange('my_test_stream', '+', '-', 1);
        $data = json_decode(json_decode(reset($messages)['content'], true)['body'], true);

        $this->assertSame($message->getName(), $data['name']);
        $this->assertSame($message->getType(), $data['type']);
        $this->assertSame($message->getId(), $data['id']);
        $this->assertSame($message->getPayload(), $data['payload']);
        $this->assertSame($message->getCreated(), $data['created']);
    }
}
