<?php

namespace TailgateApi\Middleware;

use PDO;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use TailgateApi\Events\ApiSubscriber;
use TailgateApi\Events\EventViewSubscriber;
use TailgateApi\Events\LoggerDomainEventSubscriber;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;
use Tailgate\Domain\Model\Season\SeasonProjectionInterface;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;
use Tailgate\Domain\Model\User\UserProjectionInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\GroupProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\PersistDomainEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\SeasonProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\TeamProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\UserProjectorEventSubscriber;


// add subscribers
class EventSubscribersMiddleware implements MiddlewareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $publisher = $this->container->get(EventPublisherInterface::class);
        $publisher->subscribe(new UserProjectorEventSubscriber($this->container->get(UserProjectionInterface::class)));
        $publisher->subscribe(new GroupProjectorEventSubscriber($this->container->get(GroupProjectionInterface::class)));
        $publisher->subscribe(new TeamProjectorEventSubscriber($this->container->get(TeamProjectionInterface::class)));
        $publisher->subscribe(new SeasonProjectorEventSubscriber($this->container->get(SeasonProjectionInterface::class)));
        $publisher->subscribe(new PersistDomainEventSubscriber($this->container->get(EventStoreInterface::class)));
        $publisher->subscribe(new LoggerDomainEventSubscriber($this->container->get(LoggerInterface::class)));
        $publisher->subscribe(new EventViewSubscriber($this->container->get(PDO::class)));
        $publisher->subscribe(new ApiSubscriber($this->container->get(PDO::class)));

        return $handler->handle($request);
    }
}