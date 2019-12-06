<?php

use Slim\App;
use League\Tactician\Setup\QuickStart;

use Tailgate\Application\Query\User\UserQuery;
use Tailgate\Application\Query\User\UserQueryHandler;
use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Application\Query\User\AllUsersQueryHandler;
use Tailgate\Application\Query\User\UserEmailQuery;
use Tailgate\Application\Query\User\UserEmailQueryHandler;
use Tailgate\Application\Query\User\UserResetPasswordTokenQuery;
use Tailgate\Application\Query\User\UserResetPasswordTokenQueryHandler;

use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\GroupQueryHandler;
use Tailgate\Application\Query\Group\GroupInviteCodeQuery;
use Tailgate\Application\Query\Group\GroupInviteCodeQueryHandler;
use Tailgate\Application\Query\Group\QueryGroupsQuery;
use Tailgate\Application\Query\Group\QueryGroupsQueryHandler;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Application\Query\Group\AllGroupsQueryHandler;

use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Application\Query\Team\TeamQueryHandler;
use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Application\Query\Team\AllTeamsQueryHandler;

use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Application\Query\Season\SeasonQueryHandler;
use Tailgate\Application\Query\Season\AllSeasonsQuery;
use Tailgate\Application\Query\Season\AllSeasonsQueryHandler;

return function (App $app) {
    $container = $app->getContainer();

    // query bus
    $container->set('queryBus', function ($container) {
        return QuickStart::create([
            UserQuery::class => new UserQueryHandler($container->get('viewRepository.user'), $container->get('transformer.user')),
            UserEmailQuery::class => new UserEmailQueryHandler($container->get('viewRepository.user'), $container->get('transformer.user')),
            UserResetPasswordTokenQuery::class => new UserResetPasswordTokenQueryHandler($container->get('viewRepository.user'), $container->get('transformer.user')),
            AllUsersQuery::class => new AllUsersQueryHandler($container->get('viewRepository.user'), $container->get('transformer.user')),

            GroupQuery::class => new GroupQueryHandler(
                $container->get('viewRepository.group'),
                $container->get('viewRepository.member'),
                $container->get('viewRepository.player'),
                $container->get('viewRepository.score'),
                $container->get('viewRepository.follow'),
                $container->get('transformer.group')
            ),
            GroupInviteCodeQuery::class => new GroupInviteCodeQueryHandler(
                $container->get('viewRepository.group'),
                $container->get('transformer.group')
            ),
            QueryGroupsQuery::class => new QueryGroupsQueryHandler(
                $container->get('viewRepository.group'),
                $container->get('transformer.group')
            ),
            AllGroupsQuery::class => new AllGroupsQueryHandler($container->get('viewRepository.group'), $container->get('transformer.group')),
            
            TeamQuery::class => new TeamQueryHandler(
                $container->get('viewRepository.team'),
                $container->get('viewRepository.follow'),
                $container->get('viewRepository.game'),
                $container->get('transformer.team')
            ),
            AllTeamsQuery::class => new AllTeamsQueryHandler($container->get('viewRepository.team'), $container->get('transformer.team')),
            
            SeasonQuery::class => new SeasonQueryHandler(
                $container->get('viewRepository.season'),
                $container->get('viewRepository.game'),
                $container->get('transformer.season')
            ),
            AllSeasonsQuery::class => new AllSeasonsQueryHandler($container->get('viewRepository.season'), $container->get('transformer.season')),
        ]);
    });
};
