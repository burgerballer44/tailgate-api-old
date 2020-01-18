<?php

namespace TailgateApi\Events;

use Psr\Log\LoggerInterface;
use Burger\Event;
use Burger\EventPublisherInterface;
use Burger\EventSubscriberInterface;

class LoggerDomainEventSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Event $event)
    {
        $this->logger->info(get_class($event->data));
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on('*', [$this, 'handle']);
    }
}
