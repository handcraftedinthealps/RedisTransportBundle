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

### PHP

That php is not closing the socket connection to redis when waiting for incomming messages
it is needed to set the `default_socket_timeout` to `-1` in your `php.ini` configuration file.

```ini
; php.ini

; Default timeout for socket based streams (seconds)
; http://php.net/default-socket-timeout
default_socket_timeout = -1
```

### Symfony

When using the **Symfony/FrameworkBundle** you can configure the following thing

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
