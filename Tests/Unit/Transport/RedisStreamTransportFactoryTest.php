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

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport\RedisStreamTransport;
use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Transport\RedisStreamTransportFactory;
use PHPUnit\Framework\TestCase;

class RedisStreamTransportFactoryTest extends TestCase
{
    /**
     * @var RedisStreamTransportFactory
     */
    private $redisStreamTransportFactory;

    public function setUp()
    {
        $this->redisStreamTransportFactory = new RedisStreamTransportFactory();
    }

    public function testSupports()
    {
        $this->assertTrue($this->redisStreamTransportFactory->supports('redis-stream://test', []));
    }

    public function testSupportsFalse()
    {
        $this->assertFalse($this->redisStreamTransportFactory->supports('other://test', []));
    }

    public function testCreateTransport()
    {
        /** @var RedisStreamTransport $transport */
        $transport = $this->redisStreamTransportFactory->createTransport(
            'redis-stream://127.0.0.1:6379/stream', []
        );

        $this->assertInstanceOf(RedisStreamTransport::class, $transport);
    }

    public function testCreateGroupCustomerTransport()
    {
        /** @var RedisStreamTransport $transport */
        $transport = $this->redisStreamTransportFactory->createTransport(
            'redis-stream://127.0.0.1:6379/stream/group/customer', []
        );

        $this->assertInstanceOf(RedisStreamTransport::class, $transport);
    }
}
