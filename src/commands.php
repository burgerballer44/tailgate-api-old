<?php

use Slim\App;
use League\Tactician\Setup\QuickStart;

use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Command\Group\AddMemberToGroupHandler;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Command\Group\AddPlayerToGroupHandler;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupHandler;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Command\Group\DeleteGroupHandler;
use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Command\Group\DeleteMemberHandler;
use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Command\Group\DeletePlayerHandler;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Command\Group\DeleteScoreHandler;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Command\Group\SubmitScoreForGroupHandler;
use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Application\Command\Group\UpdateGroupHandler;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Command\Group\UpdateMemberHandler;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;
use Tailgate\Application\Command\Group\UpdateScoreForGroupHandler;

use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Application\Command\Season\AddGameHandler;
use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Command\Season\DeleteGameHandler;
use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Command\Season\DeleteSeasonHandler;
use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Command\Season\CreateSeasonHandler;
use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Application\Command\Season\UpdateGameScoreHandler;
use Tailgate\Application\Command\Season\UpdateSeasonCommand;
use Tailgate\Application\Command\Season\UpdateSeasonHandler;

use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Command\Team\AddTeamHandler;
use Tailgate\Application\Command\Team\DeleteFollowCommand;
use Tailgate\Application\Command\Team\DeleteFollowHandler;
use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Command\Team\DeleteTeamHandler;
use Tailgate\Application\Command\Team\FollowTeamCommand;
use Tailgate\Application\Command\Team\FollowTeamHandler;
use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Application\Command\Team\UpdateTeamHandler;

use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Application\Command\User\ActivateUserHandler;
use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Application\Command\User\DeleteUserHandler;
use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Application\Command\User\RegisterUserHandler;
use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Command\User\RequestPasswordResetHandler;
use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Application\Command\User\UpdateEmailHandler;
use Tailgate\Application\Command\User\ResetPasswordCommand;
use Tailgate\Application\Command\User\ResetPasswordHandler;
use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Application\Command\User\UpdateUserHandler;

return function (App $app) {
    $container = $app->getContainer();

    // command bus
    $container->set('commandBus', function ($container) {
        return QuickStart::create([

            AddMemberToGroupCommand::class => new AddMemberToGroupHandler( $container->get('repository.group')),
            AddPlayerToGroupCommand::class => new AddPlayerToGroupHandler( $container->get('repository.group')),
            CreateGroupCommand::class => new CreateGroupHandler(
                $container->get('repository.group'), $container->get('stringShuffler')
            ),
            DeleteGroupCommand::class => new DeleteGroupHandler($container->get('repository.group')),
            DeleteMemberCommand::class => new DeleteMemberHandler($container->get('repository.group')),
            DeletePlayerCommand::class => new DeletePlayerHandler($container->get('repository.group')),
            DeleteScoreCommand::class => new DeleteScoreHandler($container->get('repository.group')),
            SubmitScoreForGroupCommand::class => new SubmitScoreForGroupHandler($container->get('repository.group')),
            UpdateGroupCommand::class => new UpdateGroupHandler($container->get('repository.group')),
            UpdateMemberCommand::class => new UpdateMemberHandler($container->get('repository.group')),
            UpdateScoreForGroupCommand::class => new UpdateScoreForGroupHandler($container->get('repository.group')),


            AddGameCommand::class => new AddGameHandler($container->get('repository.season')),
            CreateSeasonCommand::class => new CreateSeasonHandler($container->get('repository.season')),
            DeleteGameCommand::class => new DeleteGameHandler($container->get('repository.season')),
            DeleteSeasonCommand::class => new DeleteSeasonHandler($container->get('repository.season')),
            UpdateGameScoreCommand::class => new UpdateGameScoreHandler($container->get('repository.season')),            
            UpdateSeasonCommand::class => new UpdateSeasonHandler($container->get('repository.season')),   


            AddTeamCommand::class => new AddTeamHandler($container->get('repository.team')),
            DeleteFollowCommand::class => new DeleteFollowHandler($container->get('repository.team')),
            DeleteTeamCommand::class => new DeleteTeamHandler($container->get('repository.team')),
            FollowTeamCommand::class => new FollowTeamHandler($container->get('repository.team')),
            UpdateTeamCommand::class => new UpdateTeamHandler($container->get('repository.team')),


            ActivateUserCommand::class => new ActivateUserHandler($container->get('repository.user')),
            DeleteUserCommand::class => new DeleteUserHandler($container->get('repository.user')),
            RegisterUserCommand::class => new RegisterUserHandler(
                $container->get('repository.user'),
                $container->get('passwordHashing')
            ),
            RequestPasswordResetCommand::class => new RequestPasswordResetHandler(
                $container->get('repository.user'),
                $container->get('stringShuffler')
            ),
            ResetPasswordCommand::class => new ResetPasswordHandler(
                $container->get('repository.user'),
                $container->get('passwordHashing')
            ),
            UpdateEmailCommand::class => new UpdateEmailHandler($container->get('repository.user')),
            UpdateUserCommand::class => new UpdateUserHandler($container->get('repository.user')),         
        ]);
    });
};
