# RedisTransportBundle ⛰

[![GitHub license](https://img.shields.io/github/license/handcraftedinthealps/RedisTransportBundle.svg)](https://github.com/handcraftedinthealps/RedisTransportBundle/blob/master/LICENSE)
[![GitHub tag (latest SemVer)](https://img.shields.io/github/tag/handcraftedinthealps/RedisTransportBundle.svg)](https://github.com/handcraftedinthealps/RedisTransportBundle/releases)
[![CircleCI](https://circleci.com/gh/sulu/sulu/tree/develop.svg?style=shield)](https://circleci.com/gh/handcraftedinthealps/RedisTransportBundle/tree/master)

A symfony messenger transport implementation for redis streams.

## Requirements

 - PHP: **`^7.1`**
    - Redis Extension: **`^4.2`**
 - Redis Server: **`^5.0`**

## Installation

You need [composer](https://getcomposer.org) to install this bundle to your symfony application.

```bash
composer require handcraftedinthealps/redis-transport-bundle
```

## Configuration

### Symfony

When using the **symfony/framework-bundle** you can configure the following:

```yaml
# config/packages/framework.yaml
framework:
    messenger:
        routing:
            'HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message\DomainEventMessage':
                senders: ['redis_stream']
        transports:
            redis_stream: 'redis-stream://127.0.0.1:6379/my_stream/my_group/my_consumer'
```

You can then send a DomainEventMessage or your custom Message over the redis stream:

```php
use HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message\DomainEventMessage;

$this->messageBus->dispatch(
    new DomainEventMessage(
        'mountain.modified',    // the custom event action
        'mountain',             // the model which has been changed
        '1',                    // the model id or uuid
        [                       // the model payload
            'id' => '1',
            'name' => 'Piz Buin',
            'height' => 3312,
        ]
    )
);
```

And you can consume the messages with:

```bash
bin/console messenger:consume-messages redis_stream
```

Have also a look at the the [messenger component documentation](https://symfony.com/doc/current/components/messenger.html) and [messenger usage documentation](https://symfony.com/doc/current/messenger.html).


## Commands

Messages in streams wont be removed by default. Therefor this bundle provides a command:

```
bin/console redis-transport:trim <redis-dsn> --maxlen 1000
```
