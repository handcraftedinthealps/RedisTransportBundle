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

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Tests\Unit\Command;

use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Command\TrimRedisStreamCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class TrimRedisStreamCommandTest extends TestCase
{
    use ProphecyTrait;

    public function testExecute(): void
    {
        $redis = $this->prophesize(\Redis::class);

        $input = new ArgvInput([TrimRedisStreamCommand::getDefaultName(), 'redis-dsn://password@127.0.0.1:6739/stream/group/consumer']);
        $output = new NullOutput();

        $redis->connect('127.0.0.1', '6739')->shouldBeCalled();
        $redis->getLastError()->willReturn(null)->shouldBeCalled();
        $redis->auth('password')->shouldBeCalled();
        $redis->xtrim('stream', '1000', true)
            ->willReturn(1)
            ->shouldBeCalled();

        $command = new TrimRedisStreamCommand($redis->reveal());
        $command->run($input, $output);
    }
}
