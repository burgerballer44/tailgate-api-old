<?php

use Slim\App;

use Verraes\ClassFunctions\ClassFunctions;
use Tailgate\Application\Validator\ValidationInflector;
use Tailgate\Application\Validator\RegisterUserCommandValidator;
use Tailgate\Application\Validator\AddMemberToGroupCommandValidator;
use Tailgate\Application\Validator\CreateGroupCommandValidator;
use Tailgate\Application\Validator\SubmitScoreForGroupCommandValidator;
use Tailgate\Application\Validator\AddTeamCommandValidator;
use Tailgate\Application\Validator\FollowTeamCommandValidator;
use Tailgate\Application\Validator\AddGameCommandValidator;
use Tailgate\Application\Validator\AddGameScoreCommandValidator;
use Tailgate\Application\Validator\CreateSeasonCommandValidator;

return function (App $app) {

    $container = $app->getContainer();

    // validation inflector
    // RegisterUserCommand turns into RegisterUserCommandValidator
    $container->set('validationInflector', function ($container) {
        return new class(
            $container,
        ) {
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
    $container->set('RegisterUserCommandValidator', function ($container) {
        return new RegisterUserCommandValidator(
            $container->get('viewRepository.user')
        );
    });
    $container->set('AddMemberToGroupCommandValidator', function ($container) {
        return new AddMemberToGroupCommandValidator();
    });
    $container->set('CreateGroupCommandValidator', function ($container) {
        return new CreateGroupCommandValidator();
    });
    $container->set('SubmitScoreForGroupCommandValidator', function ($container) {
        return new SubmitScoreForGroupCommandValidator();
    });
    $container->set('AddTeamCommandValidator', function ($container) {
        return new AddTeamCommandValidator();
    });
    $container->set('FollowTeamCommandValidator', function ($container) {
        return new FollowTeamCommandValidator();
    });
    $container->set('AddGameCommandValidator', function ($container) {
        return new AddGameCommandValidator();
    });
    $container->set('AddGameScoreCommandValidator', function ($container) {
        return new AddGameScoreCommandValidator();
    });
    $container->set('CreateSeasonCommandValidator', function ($container) {
        return new CreateSeasonCommandValidator();
    });

};