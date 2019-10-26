<?php

use Slim\App;

use Verraes\ClassFunctions\ClassFunctions;
use TailgateApi\Validators\RegisterUserCommandValidator;
use TailgateApi\Validators\ActivateUserCommandValidator;
use TailgateApi\Validators\UpdateUserCommandValidator;
use TailgateApi\Validators\UpdateEmailCommandValidator;
use TailgateApi\Validators\UpdatePasswordCommandValidator;

use TailgateApi\Validators\AddMemberToGroupCommandValidator;
use TailgateApi\Validators\UpdateMemberCommandValidator;
use TailgateApi\Validators\AddPlayerToGroupCommandValidator;
use TailgateApi\Validators\CreateGroupCommandValidator;
use TailgateApi\Validators\UpdateGroupCommandValidator;
use TailgateApi\Validators\SubmitScoreForGroupCommandValidator;
use TailgateApi\Validators\UpdateScoreForGroupCommandValidator;

use TailgateApi\Validators\AddTeamCommandValidator;
use TailgateApi\Validators\UpdateTeamCommandValidator;
use TailgateApi\Validators\FollowTeamCommandValidator;

use TailgateApi\Validators\AddGameCommandValidator;
use TailgateApi\Validators\UpdateGameScoreCommandValidator;
use TailgateApi\Validators\CreateSeasonCommandValidator;
use TailgateApi\Validators\UpdateSeasonCommandValidator;

return function (App $app) {

    $container = $app->getContainer();

    // validation inflector
    // RegisterUserCommand turns into RegisterUserCommandValidator
    $container->set('validationInflector', function ($container) {
        return new class($container)
        {
            private $container;
            
            public function __construct($container)
            {
                $this->container = $container;
            }

            public function getValidatorClass($command)
            {   
                $classname = ClassFunctions::short($command) . 'Validator';

                return $this->container->make($classname);
            }
        };
    });


    // validators
    $container->set('RegisterUserCommandValidator', function ($container) {return new RegisterUserCommandValidator(
        $container->get('viewRepository.user')
    );});
    $container->set('UpdatePasswordCommandValidator', function ($container) {return new UpdatePasswordCommandValidator(
        $container->get('viewRepository.user')
    );});
    $container->set('ActivateUserCommandValidator', function ($container) {return new ActivateUserCommandValidator(
        $container->get('viewRepository.user')
    );});
    $container->set('UpdateUserCommandValidator', function ($container) {return new UpdateUserCommandValidator(
        $container->get('viewRepository.user')
    );});
    $container->set('UpdateEmailCommandValidator', function ($container) {return new UpdateEmailCommandValidator(
        $container->get('viewRepository.user')
    );});


    $container->set('CreateGroupCommandValidator', function ($container) {return new CreateGroupCommandValidator(
        $container->get('viewRepository.user'), $container->get('viewRepository.member')
    );});
    $container->set('UpdateGroupCommandValidator', function ($container) {return new UpdateGroupCommandValidator(
        $container->get('viewRepository.group'), $container->get('viewRepository.user')
    );});
    $container->set('AddMemberToGroupCommandValidator', function ($container) {return new AddMemberToGroupCommandValidator(
        $container->get('viewRepository.group'), $container->get('viewRepository.member'), $container->get('viewRepository.user')
    );});
    $container->set('UpdateMemberCommandValidator', function ($container) {return new UpdateMemberCommandValidator(
        $container->get('viewRepository.group'), $container->get('viewRepository.member')
    );});
    $container->set('SubmitScoreForGroupCommandValidator', function ($container) {return new SubmitScoreForGroupCommandValidator(
        $container->get('viewRepository.group'), $container->get('viewRepository.player'), $container->get('viewRepository.game')
    );});
    $container->set('UpdateScoreForGroupCommandValidator', function ($container) {return new UpdateScoreForGroupCommandValidator(
        $container->get('viewRepository.group'), $container->get('viewRepository.score')
    );});
    $container->set('AddPlayerToGroupCommandValidator', function ($container) {return new AddPlayerToGroupCommandValidator(
        $container->get('viewRepository.group'), $container->get('viewRepository.member'), $container->get('viewRepository.player')
    );});
    

    $container->set('AddTeamCommandValidator', function ($container) {return new AddTeamCommandValidator();});
    $container->set('UpdateTeamCommandValidator', function ($container) {return new UpdateTeamCommandValidator(
        $container->get('viewRepository.team')
    );});
    $container->set('FollowTeamCommandValidator', function ($container) {return new FollowTeamCommandValidator(
        $container->get('viewRepository.team'), $container->get('viewRepository.group'), $container->get('viewRepository.follow')
    );});
    

    $container->set('CreateSeasonCommandValidator', function ($container) {return new CreateSeasonCommandValidator();});
    $container->set('UpdateSeasonCommandValidator', function ($container) {return new UpdateSeasonCommandValidator(
        $container->get('viewRepository.season')
    );});
    $container->set('AddGameCommandValidator', function ($container) {return new AddGameCommandValidator(
        $container->get('viewRepository.season'), $container->get('viewRepository.team')
    );});
    $container->set('UpdateGameScoreCommandValidator', function ($container) {return new UpdateGameScoreCommandValidator(
        $container->get('viewRepository.season'), $container->get('viewRepository.game')
    );});

};