<?php

namespace TailgateApi\Events;

use Buttercup\Protects\DomainEvent;
use PDO;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;
use Tailgate\Domain\Model\Team\TeamDomainEvent;
use Tailgate\Domain\Model\User\UserDomainEvent;

class EventViewSubscriber implements EventSubscriberInterface
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
        $publisher->on(DomainEvent::class, [$this, 'handle']);
        $publisher->on(GroupDomainEvent::class, [$this, 'handle']);
        $publisher->on(SeasonDomainEvent::class, [$this, 'handle']);
        $publisher->on(TeamDomainEvent::class, [$this, 'handle']);
        $publisher->on(UserDomainEvent::class, [$this, 'handle']);
    }
}
