<?php

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Server;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use TailgateApi\Auth\TailgatePDOStorage;
use TailgateApi\Auth\TailgateUserCredentials;
use TailgateApi\Middleware\TransactionMiddleware;
use TailgateApi\Middleware\ValidationExceptionMiddleware;
use TailgateApi\Repository\EventViewRepository;
use TailgateApi\Transactions\PdoTransaction;
use TailgateApi\Transactions\TransactionHandlerInterface;
use Burger\EventPublisher;
use Burger\EventPublisherInterface;
use Tailgate\Infrastructure\Service\PasswordHashing\BasicPasswordHashing;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Service\Security\RandomStringInterface;
use Tailgate\Infrastructure\Service\Security\StringShuffler;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\GroupProjectionInterface;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\SeasonProjectionInterface;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\TeamProjectionInterface;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\UserProjectionInterface;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\FollowDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\PlayerDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\TeamDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;
use Tailgate\Infrastructure\Service\DataTransformer\FollowViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\GameViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\GroupViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\MemberViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\PlayerViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\ScoreViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\SeasonViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\TeamViewArrayDataTransformer;
use Tailgate\Infrastructure\Service\DataTransformer\UserViewArrayDataTransformer;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Event\PDO\EventStore;
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

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        // slim app
        App::class => function (ContainerInterface $container) {
            AppFactory::setContainer($container);
            $app = AppFactory::create();
            return $app;
        },

        // response factory
        ResponseFactoryInterface::class => function (ContainerInterface $container) {
            return $container->get(App::class)->getResponseFactory();
        },

        // pdo connection
        PDO::class => function (ContainerInterface $container) {
            $settings   = $container->get('settings')['pdo'];
            $connection = $settings['connection'];
            $host       = $settings['host'];
            $port       = $settings['port'];
            $database   = $settings['database'];
            $username   = $settings['username'];
            $password   = $settings['password'];

            return new PDO("{$connection}:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        },

        // logger
        LoggerInterface::class => function (ContainerInterface $container) {
            $settings = $container->get('settings')['logger'];
            $logger = new Logger($settings['name']);
            $processor = new UidProcessor();
            $logger->pushProcessor($processor);
            $handler = new StreamHandler($settings['path'], $settings['level']);
            $logger->pushHandler($handler);
            return $logger;
        },

        // event store
        EventStoreInterface::class => function (ContainerInterface $container) {
            return new EventStore($container->get(PDO::class));
        },

        // projections
        UserProjectionInterface::class => function (ContainerInterface $container) {
            return new UserProjection($container->get(PDO::class));
        },
        GroupProjectionInterface::class => function (ContainerInterface $container) {
            return new GroupProjection($container->get(PDO::class));
        },
        TeamProjectionInterface::class => function (ContainerInterface $container) {
            return new TeamProjection($container->get(PDO::class));
        },
        SeasonProjectionInterface::class => function (ContainerInterface $container) {
            return new SeasonProjection($container->get(PDO::class));
        },

        // event publisher
        EventPublisherInterface::class => function (ContainerInterface $container) {
            return EventPublisher::instance();
        },

        // write repositories
        UserRepositoryInterface::class => function (ContainerInterface $container) {
            return new UserRepository(
                $container->get(EventStoreInterface::class),
                $container->get(EventPublisherInterface::class)
            );
        },
        GroupRepositoryInterface::class => function (ContainerInterface $container) {
            return new GroupRepository(
                $container->get(EventStoreInterface::class),
                $container->get(EventPublisherInterface::class)
            );
        },
        TeamRepositoryInterface::class => function (ContainerInterface $container) {
            return new TeamRepository(
                $container->get(EventStoreInterface::class),
                $container->get(EventPublisherInterface::class)
            );
        },
        SeasonRepositoryInterface::class => function (ContainerInterface $container) {
            return new SeasonRepository(
                $container->get(EventStoreInterface::class),
                $container->get(EventPublisherInterface::class)
            );
        },

        // read repositories
        UserViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new UserViewRepository($container->get(PDO::class));
        },
        GroupViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new GroupViewRepository($container->get(PDO::class));
        },
        MemberViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new MemberViewRepository($container->get(PDO::class));
        },
        PlayerViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new PlayerViewRepository($container->get(PDO::class));
        },
        ScoreViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new ScoreViewRepository($container->get(PDO::class));
        },
        TeamViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new TeamViewRepository($container->get(PDO::class));
        },
        FollowViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new FollowViewRepository($container->get(PDO::class));
        },
        SeasonViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new SeasonViewRepository($container->get(PDO::class));
        },
        GameViewRepositoryInterface::class => function (ContainerInterface $container) {
            return new GameViewRepository($container->get(PDO::class));
        },
        EventViewRepository::class => function (ContainerInterface $container) {
            return new EventViewRepository($container->get(PDO::class));
        },

        // transformers
        UserDataTransformerInterface::class => function (ContainerInterface $container) {
            return new UserViewArrayDataTransformer();
        },
        MemberDataTransformerInterface::class => function (ContainerInterface $container) {
            return new MemberViewArrayDataTransformer();
        },
        PlayerDataTransformerInterface::class => function (ContainerInterface $container) {
            return new PlayerViewArrayDataTransformer();
        },
        ScoreDataTransformerInterface::class => function (ContainerInterface $container) {
            return new ScoreViewArrayDataTransformer();
        },
        FollowDataTransformerInterface::class => function (ContainerInterface $container) {
            return new FollowViewArrayDataTransformer();
        },
        GameDataTransformerInterface::class => function (ContainerInterface $container) {
            return new GameViewArrayDataTransformer();
        },
        GroupDataTransformerInterface::class => function (ContainerInterface $container) {
            return new GroupViewArrayDataTransformer(
                $container->get(MemberDataTransformerInterface::class),
                $container->get(PlayerDataTransformerInterface::class),
                $container->get(ScoreDataTransformerInterface::class),
                $container->get(FollowDataTransformerInterface::class)
            );
        },
        SeasonDataTransformerInterface::class => function (ContainerInterface $container) {
            return new SeasonViewArrayDataTransformer(
                $container->get(GameDataTransformerInterface::class),
            );
        },
        TeamDataTransformerInterface::class => function (ContainerInterface $container) {
            return new TeamViewArrayDataTransformer(
                $container->get(FollowDataTransformerInterface::class),
                $container->get(GameDataTransformerInterface::class),
            );
        },

        // password hashing
        PasswordHashingInterface::class => function (ContainerInterface $container) {
            return new BasicPasswordHashing();
        },

        // semi random string generation
        RandomStringInterface::class => function (ContainerInterface $container) {
            return new StringShuffler;
        },

        // transactions
        TransactionHandlerInterface::class => function (ContainerInterface $container) {
            return new PdoTransaction($container->get(PDO::class));
        },

        // Oauth Server
        Server::class => function (ContainerInterface $container) {

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
        },

        // middleware
        GuardMiddleware::class => function (ContainerInterface $container) {
            return new GuardMiddleware($container->get(Server::class));
        },

        ValidationExceptionMiddleware::class => function (ContainerInterface $container) {
            return new ValidationExceptionMiddleware($container->get(ResponseFactoryInterface::class));
        },

        TransactionMiddleware::class => function (ContainerInterface $container) {
            return new TransactionMiddleware($container->get(TransactionHandlerInterface::class));
        },
    ]);
};