<?php

namespace TailgateApi\Events;

use PDO;
use TailgateApi\Events\UserLoggedIn;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;

class ApiSubscriber implements EventSubscriberInterface
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
            ':aggregate_id' => (string) $event->data->getAggregateId(),
            ':type' => get_class($event->data),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ':data' => serialize($event->data)
        ]);
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(UserLoggedIn::class, [$this, 'handle']);
    }
}
