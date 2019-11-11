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

        $group->group('/users', function (Group $group) {
            $group->get('/me', \TailgateApi\Controllers\UserController::class . ':me');
            $group->patch('/me', \TailgateApi\Controllers\UserController::class . ':mePatch');
            $group->patch('/me/email', \TailgateApi\Controllers\UserController::class . ':emailPatch');
        });

        $group->group('/groups', function (Group $group) {
            $group->post('/invite-code', \TailgateApi\Controllers\GroupController::class . ':inviteCodePost');
            $group->get('', \TailgateApi\Controllers\GroupController::class . ':all');
            $group->post('', \TailgateApi\Controllers\GroupController::class . ':createPost');
            $group->get('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':view');
            $group->delete('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':groupDelete');
            $group->post('/{groupId}/member/{memberId}/player', \TailgateApi\Controllers\GroupController::class . ':playerPost');
            $group->delete('/{groupId}/player/{playerId}', \TailgateApi\Controllers\GroupController::class . ':playerDelete');
            
            $group->post('/{groupId}/member', \TailgateApi\Controllers\GroupController::class . ':memberPost');
            $group->patch('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':memberPatch');
            $group->delete('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':memberDelete');
            $group->post('/{groupId}/player/{playerId}/score', \TailgateApi\Controllers\GroupController::class . ':scorePost');
            $group->delete('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':scoreDelete');
            $group->patch('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':scorePatch');
        });

        $group->group('/teams', function (Group $group) {
            $group->get('', \TailgateApi\Controllers\TeamController::class . ':all');
            $group->post('', \TailgateApi\Controllers\TeamController::class . ':addPost');
            $group->get('/{teamId}', \TailgateApi\Controllers\TeamController::class . ':view');
            $group->patch('/{teamId}', \TailgateApi\Controllers\TeamController::class . ':teamPatch');
            $group->delete('/{teamId}', \TailgateApi\Controllers\TeamController::class . ':teamDelete');
            $group->post('/{teamId}/follow', \TailgateApi\Controllers\TeamController::class . ':followPost');
            $group->delete('/{teamId}/follow/{followId}', \TailgateApi\Controllers\TeamController::class . ':followDelete');
        });

        $group->group('/seasons', function (Group $group) {
            $group->get('', \TailgateApi\Controllers\SeasonController::class . ':all');
            $group->post('', \TailgateApi\Controllers\SeasonController::class . ':createPost');
            $group->get('/{seasonId}', \TailgateApi\Controllers\SeasonController::class . ':view');
            $group->patch('/{seasonId}', \TailgateApi\Controllers\SeasonController::class . ':seasonPatch');
            $group->delete('/{seasonId}', \TailgateApi\Controllers\SeasonController::class . ':seasonDelete');
            $group->post('/{seasonId}/game', \TailgateApi\Controllers\SeasonController::class . ':gamePost');
            $group->patch('/{seasonId}/game/{gameId}/score', \TailgateApi\Controllers\SeasonController::class . ':updateGameScorePatch');
            $group->delete('/{seasonId}/game/{gameId}', \TailgateApi\Controllers\SeasonController::class . ':gameDelete');
        });

        // admin access
        $group->group('/admin', function (Group $group) {

            $group->group('/users', function (Group $group) {
                $group->get('', \TailgateApi\Controllers\UserController::class . ':all');
                $group->get('/{userId}', \TailgateApi\Controllers\UserController::class . ':view');
                $group->patch('/{userId}', \TailgateApi\Controllers\UserController::class . ':userPatch');
                $group->delete('/{userId}', \TailgateApi\Controllers\UserController::class . ':delete');
            });

            $group->group('/groups', function (Group $group) {
                $group->get('', \TailgateApi\Controllers\GroupController::class . ':all');
                $group->post('', \TailgateApi\Controllers\GroupController::class . ':createPost');
                $group->get('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':view');
                $group->patch('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':groupPatch');
                $group->delete('/{groupId}', \TailgateApi\Controllers\GroupController::class . ':groupDelete');
                $group->post('/{groupId}/member', \TailgateApi\Controllers\GroupController::class . ':memberPost');
                $group->patch('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':memberPatch');
                $group->delete('/{groupId}/member/{memberId}', \TailgateApi\Controllers\GroupController::class . ':memberDelete');
                $group->post('/{groupId}/member/{memberId}/player', \TailgateApi\Controllers\GroupController::class . ':playerPost');
                $group->delete('/{groupId}/player/{playerId}', \TailgateApi\Controllers\GroupController::class . ':playerDelete');
                $group->post('/{groupId}/player/{playerId}/score', \TailgateApi\Controllers\GroupController::class . ':scorePost');
                $group->delete('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':scoreDelete');
                $group->patch('/{groupId}/score/{scoreId}', \TailgateApi\Controllers\GroupController::class . ':scorePatch');
            });

        })->add(\TailgateApi\Middleware\AdminMiddleware::class);

    })->add(\TailgateApi\Middleware\GuardMiddleware::class);

};