<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;
use TailgateApi\Middleware\AdminMiddleware;
use TailgateApi\Middleware\GuardMiddleware;
use TailgateApi\Middleware\TransactionMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    $app->post('/register', \TailgateApi\Actions\User\RegisterUserAction::class);
    $app->patch('/activate/{userId}', \TailgateApi\Actions\User\ActivateUserAction::class);
    $app->post('/request-reset', \TailgateApi\Actions\User\RequestResetAction::class);
    $app->patch('/reset-password', \TailgateApi\Actions\User\ResetPasswordAction::class);

    // grant a token for a user trying to access the API
    $app->post('/token', \TailgateApi\Actions\Auth\TokenAction::class);

    // API
    $app->group('/v1', function (Group $group) {

        // user
        $group->group('/users', function (Group $group) {
            $group->get('/me', \TailgateApi\Actions\User\MeAction::class);
            $group->patch('/me/email', \TailgateApi\Actions\User\UpdateEmailAction::class)->add(TransactionMiddleware::class);
        });

        // groups
        $group->group('/groups', function (Group $group) {
            $group->post('/invite-code', \TailgateApi\Actions\Group\InviteCodeByUserAction::class);
            $group->get('', \TailgateApi\Actions\Group\AllGroupsByUserAction::class);
            $group->post('', \TailgateApi\Actions\Group\CreateGroupByUserAction::class);
            $group->get('/{groupId}', \TailgateApi\Actions\Group\ViewGroupByUserAction::class);
            $group->delete('/{groupId}', \TailgateApi\Actions\Group\DeleteGroupByUserAction::class);
            $group->patch('/{groupId}/member/{memberId}', \TailgateApi\Actions\Group\UpdateMemberByUserAction::class);
            $group->delete('/{groupId}/member/{memberId}', \TailgateApi\Actions\Group\DeleteMemberByUserAction::class);
            $group->post('/{groupId}/member/{memberId}/player', \TailgateApi\Actions\Group\AddPlayerByUserAction::class);
            $group->patch('/{groupId}/player/{playerId}', \TailgateApi\Actions\Group\ChangePlayerOwnerByUserAction::class);
            $group->delete('/{groupId}/player/{playerId}', \TailgateApi\Actions\Group\DeletePlayerByUserAction::class);
            $group->post('/{groupId}/follow', \TailgateApi\Actions\Group\FollowTeamByUserAction::class);
            $group->delete('/{groupId}/follow/{followId}', \TailgateApi\Actions\Group\DeleteFollowByUserAction::class);
            $group->post('/{groupId}/player/{playerId}/score', \TailgateApi\Actions\Group\SubmitScoreByUserAction::class);
            $group->patch('/{groupId}/score/{scoreId}', \TailgateApi\Actions\Group\UpdateScoreByUserAction::class);
            $group->delete('/{groupId}/score/{scoreId}', \TailgateApi\Actions\Group\DeleteScoreByUserAction::class);
        })->add(TransactionMiddleware::class);

        // teams
        $group->group('/teams', function (Group $group) {
            $group->get('', \TailgateApi\Actions\Team\AllTeamsAction::class);
            $group->get('/sport', \TailgateApi\Actions\Team\ViewTeamsBySportAction::class);
            $group->get('/{teamId}', \TailgateApi\Actions\Team\ViewTeamAction::class);
        });

        // seasons
        $group->group('/seasons', function (Group $group) {
            $group->get('', \TailgateApi\Actions\Season\AllSeasonsAction::class);
            $group->get('/sport', \TailgateApi\Actions\Season\ViewSeasonsBySportAction::class);
            $group->get('/{seasonId}', \TailgateApi\Actions\Season\ViewSeasonAction::class);
            $group->get('/follow/{followId}', \TailgateApi\Actions\Season\AllFollowedGamesAction::class);
        });

        // admin access
        $group->group('/admin', function (Group $group) {

            // users
            $group->group('/users', function (Group $group) {
                $group->get('', \TailgateApi\Actions\User\AllUsersAction::class);
                $group->get('/{userId}', \TailgateApi\Actions\User\ViewUserAction::class);
                $group->patch('/{userId}', \TailgateApi\Actions\User\UpdateUserAction::class)->add(TransactionMiddleware::class);
                $group->delete('/{userId}', \TailgateApi\Actions\User\DeleteUserAction::class)->add(TransactionMiddleware::class);
            });

            // groups
            $group->group('/groups', function (Group $group) {
                $group->get('', \TailgateApi\Actions\Group\AllGroupsAction::class);
                $group->post('', \TailgateApi\Actions\Group\CreateGroupAction::class);
                $group->get('/{groupId}', \TailgateApi\Actions\Group\ViewGroupAction::class);
                $group->patch('/{groupId}', \TailgateApi\Actions\Group\UpdateGroupAction::class);
                $group->delete('/{groupId}', \TailgateApi\Actions\Group\DeleteGroupAction::class);
                $group->post('/{groupId}/member', \TailgateApi\Actions\Group\AddMemberAction::class);
                $group->post('/{groupId}/follow', \TailgateApi\Actions\Group\FollowTeamAction::class);
                $group->delete('/{groupId}/follow/{followId}', \TailgateApi\Actions\Group\DeleteFollowAction::class);
                $group->patch('/{groupId}/member/{memberId}', \TailgateApi\Actions\Group\UpdateMemberAction::class);
                $group->delete('/{groupId}/member/{memberId}', \TailgateApi\Actions\Group\DeleteMemberAction::class);
                $group->post('/{groupId}/member/{memberId}/player', \TailgateApi\Actions\Group\AddPlayerAction::class);
                $group->patch('/{groupId}/player/{playerId}', \TailgateApi\Actions\Group\ChangePlayerOwnerByUserAction::class);
                $group->delete('/{groupId}/player/{playerId}', \TailgateApi\Actions\Group\DeletePlayerAction::class);
                $group->post('/{groupId}/player/{playerId}/score', \TailgateApi\Actions\Group\SubmitScoreAction::class);
                $group->patch('/{groupId}/score/{scoreId}', \TailgateApi\Actions\Group\UpdateScoreAction::class);
                $group->delete('/{groupId}/score/{scoreId}', \TailgateApi\Actions\Group\DeleteScoreAction::class);
            })->add(TransactionMiddleware::class);

            // teams
            $group->group('/teams', function (Group $group) {
                $group->post('', \TailgateApi\Actions\Team\AddTeamAction::class);
                $group->patch('/{teamId}', \TailgateApi\Actions\Team\UpdateTeamAction::class);
                $group->delete('/{teamId}', \TailgateApi\Actions\Team\DeleteTeamAction::class);
            })->add(TransactionMiddleware::class);

            // seasons
            $group->group('/seasons', function (Group $group) {
                $group->post('', \TailgateApi\Actions\Season\CreateSeasonAction::class);
                $group->patch('/{seasonId}', \TailgateApi\Actions\Season\UpdateSeasonAction::class);
                $group->delete('/{seasonId}', \TailgateApi\Actions\Season\DeleteSeasonAction::class);
                $group->post('/{seasonId}/game', \TailgateApi\Actions\Season\AddGameAction::class);
                $group->patch('/{seasonId}/game/{gameId}/score', \TailgateApi\Actions\Season\UpdateGameScoreAction::class);
                $group->delete('/{seasonId}/game/{gameId}', \TailgateApi\Actions\Season\DeleteGameAction::class);
            })->add(TransactionMiddleware::class);

        })->add(AdminMiddleware::class);

    })->add(GuardMiddleware::class);

};