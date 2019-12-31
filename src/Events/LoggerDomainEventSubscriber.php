<?php

namespace TailgateApi\Events;

use Psr\Log\LoggerInterface;
use Tailgate\Common\Event\Event;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;

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
