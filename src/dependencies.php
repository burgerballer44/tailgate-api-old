<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Server;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use TailgateApi\Auth\TailgatePDOStorage;
use TailgateApi\Auth\TailgateUserCredentials;
use TailgateApi\Events\DomainEventViewSubscriber;
use TailgateApi\Events\LoggerDomainEventSubscriber;
use TailgateApi\Middleware\ValidationExceptionMiddleware;
use TailgateApi\Repository\EventViewRepository;
use Tailgate\Common\Event\DomainEventPublisher;
use Tailgate\Common\PasswordHashing\BasicPasswordHashing;
use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Common\Security\RandomStringInterface;
use Tailgate\Common\Security\StringShuffler;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonProjectionInterface;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserProjectionInterface;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\FollowViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\GameViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\GroupViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\MemberViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\PlayerDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\PlayerViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\TeamViewArrayDataTransformer;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\UserViewArrayDataTransformer;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\GroupProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\PDO\EventStore;
use Tailgate\Infrastructure\Persistence\Event\PersistDomainEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\SeasonProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\TeamProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\UserProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Projection\PDO\GroupProjection;
use Tailgate\Infrastructure\Persistence\Projection\PDO\SeasonProjection;
use Tailgate\Infrastructure\Persistence\Projection\PDO\TeamProjection;
use Tailgate\Infrastructure\Persistence\Projection\PDO\UserProjection;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\GroupRepository;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\SeasonRepository;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\TeamRepository;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\UserRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\FollowViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GameViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GroupViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\MemberViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\PlayerViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\ScoreViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\SeasonViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\TeamViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\UserViewRepository;

return function (App $app) {

    $container = $app->getContainer();

    // response factory
    $container->set(ResponseFactoryInterface::class, function () use ($app) {
        return $app->getResponseFactory();
    });

    // pdo connection
    $connection = $container->get('settings')['pdo']['connection'];
    $host = $container->get('settings')['pdo']['host'];
    $port = $container->get('settings')['pdo']['port'];
    $database = $container->get('settings')['pdo']['database'];
    $username = $container->get('settings')['pdo']['username'];
    $password = $container->get('settings')['pdo']['password'];

    $container->set(PDO::class, function ($container) use ($connection, $host, $port, $database, $username, $password)
    {
        return new PDO("{$connection}:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    });

    // logger
    $container->set(LoggerInterface::class, function ($container) {
        $settings = $container->get('settings');

        $loggerSettings = $settings['logger'];
        $logger = new Logger($loggerSettings['name']);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    });

    // event store
    $container->set(EventStoreInterface::class, function ($container) {
        return new EventStore($container->get(PDO::class));
    });

    // projections
    $container->set(UserProjectionInterface::class, function ($container) {
        return new UserProjection($container->get(PDO::class));
    });
    $container->set(GroupProjectionInterface::class, function ($container) {
        return new GroupProjection($container->get(PDO::class));
    });
    $container->set(TeamProjectionInterface::class, function ($container) {
        return new TeamProjection($container->get(PDO::class));
    });
    $container->set(SeasonProjectionInterface::class, function ($container) {
        return new SeasonProjection($container->get(PDO::class));
    });

    // event publisher
    DomainEventPublisher::instance()->subscribe(
        new UserProjectorEventSubscriber($container->get(UserProjectionInterface::class))
    );
    DomainEventPublisher::instance()->subscribe(
        new GroupProjectorEventSubscriber($container->get(GroupProjectionInterface::class))
    );
    DomainEventPublisher::instance()->subscribe(
        new TeamProjectorEventSubscriber($container->get(TeamProjectionInterface::class))
    );
    DomainEventPublisher::instance()->subscribe(
        new SeasonProjectorEventSubscriber($container->get(SeasonProjectionInterface::class))
    );
    DomainEventPublisher::instance()->subscribe(
        new PersistDomainEventSubscriber($container->get(EventStoreInterface::class))
    );
    DomainEventPublisher::instance()->subscribe(
        new LoggerDomainEventSubscriber($container->get(LoggerInterface::class))
    );
    DomainEventPublisher::instance()->subscribe(
        new DomainEventViewSubscriber($container->get(PDO::class))
    );
    $container->set(DomainEventPublisher::class, function ($container) {
        return DomainEventPublisher::instance();
    });

    // repositories
    $container->set(UserRepositoryInterface::class, function ($container) {
        return new UserRepository(
            $container->get(EventStoreInterface::class),
            $container->get(DomainEventPublisher::class)
        );
    });
    $container->set(GroupRepositoryInterface::class, function ($container) {
        return new GroupRepository(
            $container->get(EventStoreInterface::class),
            $container->get(DomainEventPublisher::class)
        );
    });
    $container->set(TeamRepositoryInterface::class, function ($container) {
        return new TeamRepository(
            $container->get(EventStoreInterface::class),
            $container->get(DomainEventPublisher::class)
        );
    });
    $container->set(SeasonRepositoryInterface::class, function ($container) {
        return new SeasonRepository(
            $container->get(EventStoreInterface::class),
            $container->get(DomainEventPublisher::class)
        );
    });

    // view repositories
    $container->set(UserViewRepositoryInterface::class, function ($container) {
        return new UserViewRepository($container->get(PDO::class));
    });
    $container->set(GroupViewRepositoryInterface::class, function ($container) {
        return new GroupViewRepository($container->get(PDO::class));
    });
    $container->set(MemberViewRepositoryInterface::class, function ($container) {
        return new MemberViewRepository($container->get(PDO::class));
    });
    $container->set(PlayerViewRepositoryInterface::class, function ($container) {
        return new PlayerViewRepository($container->get(PDO::class));
    });
    $container->set(ScoreViewRepositoryInterface::class, function ($container) {
        return new ScoreViewRepository($container->get(PDO::class));
    });
    $container->set(TeamViewRepositoryInterface::class, function ($container) {
        return new TeamViewRepository($container->get(PDO::class));
    });
    $container->set(FollowViewRepositoryInterface::class, function ($container) {
        return new FollowViewRepository($container->get(PDO::class));
    });
    $container->set(SeasonViewRepositoryInterface::class, function ($container) {
        return new SeasonViewRepository($container->get(PDO::class));
    });
    $container->set(GameViewRepositoryInterface::class, function ($container) {
        return new GameViewRepository($container->get(PDO::class));
    });
    $container->set(EventViewRepository::class, function ($container) {
        return new EventViewRepository($container->get(PDO::class));
    });

    // transformers
    $container->set(UserDataTransformerInterface::class, function ($container) {
        return new UserViewArrayDataTransformer();
    });
    $container->set(MemberDataTransformerInterface::class, function ($container) {
        return new MemberViewArrayDataTransformer();
    });
    $container->set(PlayerDataTransformerInterface::class, function ($container) {
        return new PlayerViewArrayDataTransformer();
    });
    $container->set(ScoreDataTransformerInterface::class, function ($container) {
        return new ScoreViewArrayDataTransformer();
    });
    $container->set(FollowDataTransformerInterface::class, function ($container) {
        return new FollowViewArrayDataTransformer();
    });
    $container->set(GameDataTransformerInterface::class, function ($container) {
        return new GameViewArrayDataTransformer();
    });
    $container->set(GroupDataTransformerInterface::class, function ($container) {
        return new GroupViewArrayDataTransformer(
            $container->get(MemberDataTransformerInterface::class),
            $container->get(PlayerDataTransformerInterface::class),
            $container->get(ScoreDataTransformerInterface::class),
            $container->get(FollowDataTransformerInterface::class)
        );
    });
    $container->set(SeasonDataTransformerInterface::class, function ($container) {
        return new SeasonViewArrayDataTransformer(
            $container->get(GameDataTransformerInterface::class),
        );
    });
    $container->set(TeamDataTransformerInterface::class, function ($container) {
        return new TeamViewArrayDataTransformer(
            $container->get(FollowDataTransformerInterface::class),
            $container->get(GameDataTransformerInterface::class),
        );
    });

    // password hashing
    $container->set(PasswordHashingInterface::class, function ($container) {
        return new BasicPasswordHashing();
    });

    // semi random string generation
    $container->set(RandomStringInterface::class, function ($container) {
        return new StringShuffler;
    });

    // Oauth Server
    $container->set(Server::class, function ($container) {

        $storage = new TailgatePDOStorage(
            $container->get(PDO::class),
            $container->get(PasswordHashingInterface::class),
            ['user_table' => 'user']
        );

        $server = new Server($storage,[
            'access_lifetime' => $container->get('settings')['access_lifetime']
        ]);

        // Add the "Client Credentials" grant type (cron type work)
        $server->addGrantType(new ClientCredentials($storage));

        // Add the "User Credentials" grant type (1st party apps)
        $server->addGrantType(new TailgateUserCredentials($storage));

        return $server;
    });

    // middleware
    $container->set(GuardMiddleware::class, function ($container) {
        return new GuardMiddleware($container->get(Server::class));
    });

    $container->set(ValidationExceptionMiddleware::class, function ($container) {
        return new ValidationExceptionMiddleware($container->get(ResponseFactoryInterface::class));
    });

};