<?php

use Slim\App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;

use OAuth2\Server;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\ClientCredentials;
use TailgateApi\Auth\TailgatePDOStorage;
use TailgateApi\Auth\TailgateUserCredentials;

use TailgateApi\Middleware\GuardMiddleware;

use Tailgate\Common\Event\DomainEventPublisher;
use Tailgate\Common\Event\PersistDomainEventSubscriber;
use Tailgate\Common\Event\LoggerDomainEventSubscriber;

use Tailgate\Common\PasswordHashing\BasicPasswordHashing;

use Tailgate\Common\Security\StringShuffler;

use Tailgate\Infrastructure\Persistence\Event\PDO\EventStore;

use Tailgate\Infrastructure\Persistence\Repository\Publisher\UserRepository;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\GroupRepository;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\SeasonRepository;
use Tailgate\Infrastructure\Persistence\Repository\Publisher\TeamRepository;
use Tailgate\Infrastructure\Persistence\Event\UserProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\GroupProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\SeasonProjectorEventSubscriber;
use Tailgate\Infrastructure\Persistence\Event\TeamProjectorEventSubscriber;

use Tailgate\Infrastructure\Persistence\Projection\PDO\UserProjection;
use Tailgate\Infrastructure\Persistence\Projection\PDO\GroupProjection;
use Tailgate\Infrastructure\Persistence\Projection\PDO\TeamProjection;
use Tailgate\Infrastructure\Persistence\Projection\PDO\SeasonProjection;

use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\UserViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GroupViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\MemberViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\PlayerViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\ScoreViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\TeamViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\FollowViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\SeasonViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GameViewRepository;

use Tailgate\Application\DataTransformer\UserViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\MemberViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\PlayerViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\ScoreViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\GroupViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\TeamViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\FollowViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\SeasonViewArrayDataTransformer;
use Tailgate\Application\DataTransformer\GameViewArrayDataTransformer;

return function (App $app) {

    $container = $app->getContainer();

    // pdo connection
    $connection = $container->get('settings')['pdo']['connection'];
    $host = $container->get('settings')['pdo']['host'];
    $port = $container->get('settings')['pdo']['port'];
    $database = $container->get('settings')['pdo']['database'];
    $username = $container->get('settings')['pdo']['username'];
    $password = $container->get('settings')['pdo']['password'];

    $container->set('pdo', function ($container) use (
       $connection, $host, $port, $database, $username, $password
    ) {
        return new PDO("{$connection}:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    });

    // password hashing
    $container->set('passwordHashing.basic', function ($container) {
        return new BasicPasswordHashing();
    });
    $container->set('passwordHashing', function ($container) {
        return $container->get('passwordHashing.basic');
    });

    // semi random string generation
    $container->set('stringShuffler', function ($container) {
        return new StringShuffler;
    });


    // event store
    $container->set('eventStore.pdo', function ($container) {
        return new EventStore($container->get('pdo'));
    });
    $container->set('eventStore', function ($container) {
        return $container->get('eventStore.pdo');
    });


    // Oauth
    $container->set('oauthServer', function ($container) {

        $storage = new TailgatePDOStorage(
            $container->get('pdo'),
            $container->get('passwordHashing.basic'),
            ['user_table' => 'user']
        );

        $server = new Server($storage,[
            'access_lifetime' => $container->get('settings')['access_lifetime']
        ]);

        // Add the "Client Credentials" grant type (cron type work)
        $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

        // Add the "User Credentials" grant type (1st party apps)
        $server->addGrantType(new TailgateUserCredentials($storage));

        return $server;
    });

    // group middleware
    $container->set(GuardMiddleware::class, function ($container) {
        return new GuardMiddleware($container->get('oauthServer'));
    });

    // logger
    $container->set('logger', function ($container) {
        $settings = $container->get('settings');

        $loggerSettings = $settings['logger'];
        $logger = new Logger($loggerSettings['name']);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    });


    // projections
    $container->set('projection.user.pdo', function ($container) {
        return new UserProjection($container->get('pdo'));
    });
    $container->set('projection.user', function ($container) {
        return $container->get('projection.user.pdo');
    });
    $container->set('projection.group.pdo', function ($container) {
        return new GroupProjection($container->get('pdo'));
    });
    $container->set('projection.group', function ($container) {
        return $container->get('projection.group.pdo');
    });
    $container->set('projection.team.pdo', function ($container) {
        return new TeamProjection($container->get('pdo'));
    });
    $container->set('projection.team', function ($container) {
        return $container->get('projection.team.pdo');
    });
    $container->set('projection.season.pdo', function ($container) {
        return new SeasonProjection($container->get('pdo'));
    });
    $container->set('projection.season', function ($container) {
        return $container->get('projection.season.pdo');
    });


    // event publisher
    DomainEventPublisher::instance()->subscribe(
        new UserProjectorEventSubscriber($container->get('projection.user'))
    );

    DomainEventPublisher::instance()->subscribe(
        new GroupProjectorEventSubscriber($container->get('projection.group'))
    );

    DomainEventPublisher::instance()->subscribe(
        new TeamProjectorEventSubscriber($container->get('projection.team'))
    );

    DomainEventPublisher::instance()->subscribe(
        new SeasonProjectorEventSubscriber($container->get('projection.season'))
    );

    DomainEventPublisher::instance()->subscribe(
        new PersistDomainEventSubscriber($container->get('eventStore'))
    );

    DomainEventPublisher::instance()->subscribe(
        new LoggerDomainEventSubscriber($container->get('logger'))
    );
    $container->set('eventPublisher', function ($container) {
        return DomainEventPublisher::instance();
    });

    // repositories
    $container->set('repository.user', function ($container) {
        return new UserRepository(
            $container->get('eventStore'),
            $container->get('eventPublisher')
        );
    });
    $container->set('repository.group', function ($container) {
        return new GroupRepository(
            $container->get('eventStore'),
            $container->get('eventPublisher')
        );
    });
    $container->set('repository.team', function ($container) {
        return new TeamRepository(
            $container->get('eventStore'),
            $container->get('eventPublisher')
        );
    });
    $container->set('repository.season', function ($container) {
        return new SeasonRepository(
            $container->get('eventStore'),
            $container->get('eventPublisher')
        );
    });


    // view repositories
    $container->set('viewRepository.user.pdo', function ($container) {
        return new UserViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.user', function ($container) {
        return $container->get('viewRepository.user.pdo');
    });
    $container->set('viewRepository.group.pdo', function ($container) {
        return new GroupViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.group', function ($container) {
        return $container->get('viewRepository.group.pdo');
    });
    $container->set('viewRepository.member.pdo', function ($container) {
        return new MemberViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.member', function ($container) {
        return $container->get('viewRepository.member.pdo');
    });
    $container->set('viewRepository.player.pdo', function ($container) {
        return new PlayerViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.player', function ($container) {
        return $container->get('viewRepository.player.pdo');
    });
    $container->set('viewRepository.score.pdo', function ($container) {
        return new ScoreViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.score', function ($container) {
        return $container->get('viewRepository.score.pdo');
    });
    $container->set('viewRepository.team.pdo', function ($container) {
        return new TeamViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.team', function ($container) {
        return $container->get('viewRepository.team.pdo');
    });
    $container->set('viewRepository.follow.pdo', function ($container) {
        return new FollowViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.follow', function ($container) {
        return $container->get('viewRepository.follow.pdo');
    });
    $container->set('viewRepository.season.pdo', function ($container) {
        return new SeasonViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.season', function ($container) {
        return $container->get('viewRepository.season.pdo');
    });
    $container->set('viewRepository.game.pdo', function ($container) {
        return new GameViewRepository($container->get('pdo'));
    });
    $container->set('viewRepository.game', function ($container) {
        return $container->get('viewRepository.game.pdo');
    });


    // transformers
    $container->set('transformer.user.array', function ($container) {
        return new UserViewArrayDataTransformer();
    });
    $container->set('transformer.user', function ($container) {
        return $container->get('transformer.user.array');
    });
    $container->set('transformer.member.array', function ($container) {
        return new MemberViewArrayDataTransformer();
    });
    $container->set('transformer.member', function ($container) {
        return $container->get('transformer.member.array');
    });
    $container->set('transformer.player.array', function ($container) {
        return new PlayerViewArrayDataTransformer();
    });
    $container->set('transformer.player', function ($container) {
        return $container->get('transformer.player.array');
    });
    $container->set('transformer.score.array', function ($container) {
        return new ScoreViewArrayDataTransformer();
    });
    $container->set('transformer.score', function ($container) {
        return $container->get('transformer.score.array');
    });
    $container->set('transformer.group.array', function ($container) {
        return new GroupViewArrayDataTransformer(
            $container->get('transformer.member'),
            $container->get('transformer.player'),
            $container->get('transformer.score')
        );
    });
    $container->set('transformer.group', function ($container) {
        return $container->get('transformer.group.array');
    });
    $container->set('transformer.follow.array', function ($container) {
        return new FollowViewArrayDataTransformer();
    });
    $container->set('transformer.follow', function ($container) {
        return $container->get('transformer.follow.array');
    });
    $container->set('transformer.team.array', function ($container) {
        return new TeamViewArrayDataTransformer($container->get('transformer.follow'));
    });
    $container->set('transformer.team', function ($container) {
        return $container->get('transformer.team.array');
    });
    $container->set('transformer.game.array', function ($container) {
        return new GameViewArrayDataTransformer();
    });
    $container->set('transformer.game', function ($container) {
        return $container->get('transformer.game.array');
    });
    $container->set('transformer.season.array', function ($container) {
        return new SeasonViewArrayDataTransformer($container->get('transformer.game'));
    });
    $container->set('transformer.season', function ($container) {
        return $container->get('transformer.season.array');
    });
};