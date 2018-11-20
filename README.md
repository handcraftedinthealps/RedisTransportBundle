# RedisTransportBundle

A symfony messenger transport implementation for redis streams.

## Requirements

 - PHP: **`^7.1`**
    - Redis Extension: **`^4.2`**
 - Redis Server: **`^5.0`**

## Installation

You need [composer](https://getcomposer.org) to install this bundle to your symfony application.

```bash
composer require handcraftedinthealps/redis-transport-bundle:dev-master
```

## Configuration

When using the **Symfony/FrameworkBundle** you can configure the following thing

```yaml
framework:
    messenger:
        routing:
            'Your\Message':
                senders: ['redis_stream']
        transports:
            redis_stream: 'redis-stream://127.0.0.1:6379/my_stream'
```
