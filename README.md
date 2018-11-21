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
            'Your\Message':
                senders: ['redis_stream']
        transports:
            redis_stream: 'redis-stream://127.0.0.1:6379/my_stream'
```
