<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;

return function (App $app) {

    $container = $app->getContainer();

    $app->post('/register', \TailgateApi\Controllers\UserController::class . ':registerPost');
    $app->patch('/activate/{userId}', \TailgateApi\Controllers\UserController::class . ':activate');
    $app->post('/request-reset', \TailgateApi\Controllers\UserController::class . ':requestReset');
    $app->patch('/reset-password', \TailgateApi\Controllers\UserController::class . ':passwordPatch');

    // grant a token for a user trying to access the API
    $app->post('/token', \TailgateApi\Controllers\AuthController::class . ':token');

    // API
    $app->group('/v1', function (Group $group) {

        // user
        $group->group('/users', function (Group $group) {
            $group->get('/me', \TailgateApi\Controllers\UserController::class . ':me');
            $group->patch('/me', \TailgateApi\Controllers\UserController::class . ':mePatch');
            $group->patch('/me/email', \TailgateApi\Controllers\UserController::class . ':emailPatch');
        });

        // groups
        $group->group('/groups', function (Group $group) {
            $group->post('/invite-code', \TailgateApi\Controllers\GroupController::class . ':inviteCodePost');
            $group->get('', \TailgateApi\Controllers\GroupController::class . ':all');
            $group->post('', \TailgateApi\Controllers\GroupController::class . ':createPost');
            $group->get('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':view');
            $group->delete('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':groupDelete');
            $group->post('/{groupId}/member/{memberId}/player', \TailgateApi\Controllers\GroupController::class . ':playerPost');
            $group->delete('/{groupId}/player/{playerId}', \TailgateApi\Controllers\GroupController::class . ':playerDelete');
            $group->post('/{groupId}/follow', \TailgateApi\Controllers\GroupController::class . ':followPost');
            $group->delete('/{groupId}/follow/{followId}', \TailgateApi\Controllers\GroupController::class . ':followDelete');
            $group->patch('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':memberPatch');
            $group->delete('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':memberDelete');
            $group->post('/{groupId}/player/{playerId}/score', \TailgateApi\Controllers\GroupController::class . ':scorePost');
            $group->patch('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':scorePatch');
            $group->delete('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':scoreDelete');
        });

        // teams
        $group->group('/teams', function (Group $group) {
            $group->get('', \TailgateApi\Controllers\TeamController::class . ':all');
            $group->get('/{teamId}', \TailgateApi\Controllers\TeamController::class . ':view');
        });

        // seasons
        $group->group('/seasons', function (Group $group) {
            $group->get('', \TailgateApi\Controllers\SeasonController::class . ':all');
            $group->get('/{seasonId}', \TailgateApi\Controllers\SeasonController::class . ':view');
        });

        // admin access
        $group->group('/admin', function (Group $group) {

            // users
            $group->group('/users', function (Group $group) {
                $group->get('', \TailgateApi\Controllers\UserController::class . ':all');
                $group->get('/{userId}', \TailgateApi\Controllers\UserController::class . ':view');
                $group->patch('/{userId}', \TailgateApi\Controllers\UserController::class . ':userPatch');
                $group->delete('/{userId}', \TailgateApi\Controllers\UserController::class . ':delete');
            });

            // groups
            $group->group('/groups', function (Group $group) {
                $group->get('', \TailgateApi\Controllers\GroupController::class . ':adminAll');
                $group->post('', \TailgateApi\Controllers\GroupController::class . ':adminCreatePost');
                $group->get('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':adminView');
                $group->patch('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':adminGroupPatch');
                $group->delete('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':adminGroupDelete');
                $group->post('/{groupId}/member', \TailgateApi\Controllers\GroupController::class . ':adminMemberPost');
                $group->post('/{groupId}/follow', \TailgateApi\Controllers\GroupController::class . ':adminFollowPost');
                $group->delete('/{groupId}/follow/{followId}', \TailgateApi\Controllers\GroupController::class . ':adminFollowDelete');
                $group->patch('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':adminMemberPatch');
                $group->delete('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':adminMemberDelete');
                $group->post('/{groupId}/member/{memberId}/player', \TailgateApi\Controllers\GroupController::class . ':adminPlayerPost');
                $group->delete('/{groupId}/player/{playerId}', \TailgateApi\Controllers\GroupController::class . ':adminPlayerDelete');
                $group->post('/{groupId}/player/{playerId}/score', \TailgateApi\Controllers\GroupController::class . ':adminScorePost');
                $group->delete('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':adminScoreDelete');
                $group->patch('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':adminScorePatch');
            });

            // teams
            $group->group('/teams', function (Group $group) {
                $group->post('', \TailgateApi\Controllers\TeamController::class . ':addPost');
                $group->patch('/{teamId}', \TailgateApi\Controllers\TeamController::class . ':teamPatch');
                $group->delete('/{teamId}', \TailgateApi\Controllers\TeamController::class . ':teamDelete');
            });

            // seasons
            $group->group('/seasons', function (Group $group) {
                $group->post('', \TailgateApi\Controllers\SeasonController::class . ':createPost');
                $group->patch('/{seasonId}', \TailgateApi\Controllers\SeasonController::class . ':seasonPatch');
                $group->delete('/{seasonId}', \TailgateApi\Controllers\SeasonController::class . ':seasonDelete');
                $group->post('/{seasonId}/game', \TailgateApi\Controllers\SeasonController::class . ':gamePost');
                $group->patch('/{seasonId}/game/{gameId}/score', \TailgateApi\Controllers\SeasonController::class . ':updateGameScorePatch');
                $group->delete('/{seasonId}/game/{gameId}', \TailgateApi\Controllers\SeasonController::class . ':gameDelete');
            });

        })->add(\TailgateApi\Middleware\AdminMiddleware::class);

    })->add(\TailgateApi\Middleware\GuardMiddleware::class);

};