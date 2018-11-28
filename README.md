# RedisTransportBundle

A symfony messenger transport implementation for redis streams.

## Requirements

 - PHP: **`^7.1`**
    - Redis Extension: **`^4.2`**
    - Availability to use `ini_set` to set `default_socket_timeout` to `-1` for long running process.
 - Redis Server: **`^5.0`**

## Installation

You need [composer](https://getcomposer.org) to install this bundle to your symfony application.

```bash
composer require handcraftedinthealps/redis-transport-bundle:dev-master
```

## Configuration

### Symfony

When using the **Symfony/FrameworkBundle** you can configure the following:

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
        'contact.modified',     // the custom event action
        'contact',              // the model which has been changed
        '1',                    // the model id or uuid
        [                       // the model payload
            'id' => '1',
            'firstName' => 'Heidi',
        ]
    )
);
```
