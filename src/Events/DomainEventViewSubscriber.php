<?php

namespace TailgateApi\Events;

use Buttercup\Protects\DomainEvent;
use Tailgate\Common\Event\EventSubscriberInterface;
use PDO;

class DomainEventViewSubscriber implements EventSubscriberInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle($event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO event_log (aggregate_id, type, created_at, data)
            VALUES (:aggregate_id, :type, :created_at, :data)'
        );

        $stmt->execute([
            ':aggregate_id' => (string) $event->getAggregateId(),
            ':type' => get_class($event),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ':data' => serialize($event)
        ]);
    }

    public function isSubscribedTo($event)
    {
        return $event instanceof DomainEvent;
    }
}
