<?php

namespace TailgateApi\Events;

use Psr\Log\LoggerInterface;
use Tailgate\Common\Event\EventSubscriberInterface;

class LoggerDomainEventSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle($event)
    {
        $this->logger->info(get_class($event));
    }

    public function isSubscribedTo($event)
    {
        return true;
    }
}
