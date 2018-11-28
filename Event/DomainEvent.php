<?php

declare(strict_types=1);

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Event;

class DomainEvent
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var string
     */
    protected $created;

    public function __construct(string $name, string $type, string $id, array $payload = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->payload = $payload;
        $this->created = new \DateTimeImmutable();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'id' => $this->id,
            'payload' => $this->payload,
            'created' => $this->created->format('c'),
        ];
    }
}
