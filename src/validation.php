<?php

use Slim\App;

use Verraes\ClassFunctions\ClassFunctions;
use TailgateApi\Validators\RegisterUserCommandValidator;
use TailgateApi\Validators\ActivateUserCommandValidator;
use TailgateApi\Validators\UpdateUserCommandValidator;
use TailgateApi\Validators\UpdateEmailCommandValidator;
use TailgateApi\Validators\AddMemberToGroupCommandValidator;
use TailgateApi\Validators\CreateGroupCommandValidator;
use TailgateApi\Validators\SubmitScoreForGroupCommandValidator;
use TailgateApi\Validators\AddTeamCommandValidator;
use TailgateApi\Validators\FollowTeamCommandValidator;
use TailgateApi\Validators\AddGameCommandValidator;
use TailgateApi\Validators\AddGameScoreCommandValidator;
use TailgateApi\Validators\CreateSeasonCommandValidator;
use TailgateApi\Validators\UpdatePasswordCommandValidator;

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
    $container->set('RegisterUserCommandValidator', function ($container) {return new RegisterUserCommandValidator($container->get('viewRepository.user'));});
    $container->set('UpdatePasswordCommandValidator', function ($container) {return new UpdatePasswordCommandValidator();});
    $container->set('ActivateUserCommandValidator', function ($container) {return new ActivateUserCommandValidator($container->get('viewRepository.user'));});
    $container->set('UpdateUserCommandValidator', function ($container) {return new UpdateUserCommandValidator($container->get('viewRepository.user'));});
    $container->set('UpdateEmailCommandValidator', function ($container) {return new UpdateEmailCommandValidator($container->get('viewRepository.user'));});


    $container->set('CreateGroupCommandValidator', function ($container) {return new CreateGroupCommandValidator();});
    $container->set('AddMemberToGroupCommandValidator', function ($container) {return new AddMemberToGroupCommandValidator();});
    $container->set('SubmitScoreForGroupCommandValidator', function ($container) {return new SubmitScoreForGroupCommandValidator();});
    

    $container->set('AddTeamCommandValidator', function ($container) {return new AddTeamCommandValidator();});
    $container->set('FollowTeamCommandValidator', function ($container) {return new FollowTeamCommandValidator();});
    

    $container->set('CreateSeasonCommandValidator', function ($container) {return new CreateSeasonCommandValidator();});
    $container->set('AddGameCommandValidator', function ($container) {return new AddGameCommandValidator();});
    $container->set('AddGameScoreCommandValidator', function ($container) {return new AddGameScoreCommandValidator();});

};