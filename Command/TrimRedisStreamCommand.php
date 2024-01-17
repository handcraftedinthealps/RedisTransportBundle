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

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrimRedisStreamCommand extends Command
{
    protected static $defaultName = 'redis-transport:trim';

    /**
     * @var \Redis|null
     */
    private $redis;

    public function __construct(?\Redis $redis = null)
    {
        parent::__construct(self::$defaultName);

        $this->redis = $redis;
    }

    protected function configure(): void
    {
        $this->setDescription('Trim redis-stream to a maximum length for a given DSN.');
        $this->addArgument('redis-dsn', InputArgument::REQUIRED);
        $this->addOption('maxlen', null, InputOption::VALUE_REQUIRED, '', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $redisStreamDsn */
        $redisStreamDsn = $input->getArgument('redis-dsn');

        /** @var string $maxLength */
        $maxLength = $input->getOption('maxlen');

        /** @var array{host: string, port: int, user?: string, path: string}|false $parsedUrl */
        $parsedUrl = \parse_url($redisStreamDsn);

        if (false === $parsedUrl) {
            throw new \InvalidArgumentException(\sprintf('The given dsn("%s") for the redis connection was not valid.', $redisStreamDsn));
        }

        $dsnParts = \explode('/', $parsedUrl['path']);

        $stream = $dsnParts[1];
        $auth = $parsedUrl['user'] ?? null;

        $redis = $this->getRedis();
        $redis->connect($parsedUrl['host'], $parsedUrl['port']);
        if ($auth) {
            $redis->auth($auth);
        }

        /** @var int $x */
        $x = $redis->xtrim($stream, (string) $maxLength, true);
        if ($errorMessage = $redis->getLastError()) {
            throw new \RuntimeException($errorMessage);
        }

        $io = new SymfonyStyle($input, $output);
        $io->success(\sprintf('Trimed successfully %s messages', (string) $x));

        return 0;
    }

    private function getRedis(): \Redis
    {
        if ($this->redis) {
            return $this->redis;
        }

        return new \Redis();
    }
}
