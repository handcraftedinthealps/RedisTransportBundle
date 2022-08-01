<?php

declare(strict_types=1);

namespace HandcraftedInTheAlps\Bundle\RedisTransportBundle\Message;

class DomainEventMessage
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
     * @var mixed[]
     */
    protected $payload;

    /**
     * @var string
     */
    protected $created;

    /**
     * @param mixed[] $payload
     */
    public function __construct(string $name, string $type, string $id, array $payload = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->payload = $payload;
        $this->created = (new \DateTimeImmutable())->format('c');
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

    /**
     * @return mixed[]
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @return array{
     *     name: string,
     *     type: string,
     *     id: string,
     *     payload: mixed[],
     *     created: string,
     * }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'id' => $this->id,
            'payload' => $this->payload,
            'created' => $this->created,
        ];
    }
}
