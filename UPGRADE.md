# 1.1.0

## Upgrade to Symfony 4.3

The redis stream implementation has been moved to Symfony Core.
This bundle is not longer needed to use redis stream with the 
messenger component. The 1.1.0 is just a compatible implementation
providing the `DomainEventMessage`.
