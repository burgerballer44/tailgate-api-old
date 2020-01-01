<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\App;
use TailgateApi\Validators\ActivateUserCommandValidator;
use TailgateApi\Validators\AddGameCommandValidator;
use TailgateApi\Validators\AddMemberToGroupCommandValidator;
use TailgateApi\Validators\AddPlayerToGroupCommandValidator;
use TailgateApi\Validators\AddTeamCommandValidator;
use TailgateApi\Validators\CreateGroupCommandValidator;
use TailgateApi\Validators\CreateSeasonCommandValidator;
use TailgateApi\Validators\FollowTeamCommandValidator;
use TailgateApi\Validators\RegisterUserCommandValidator;
use TailgateApi\Validators\RequestPasswordResetCommandValidator;
use TailgateApi\Validators\ResetPasswordCommandValidator;
use TailgateApi\Validators\SubmitScoreForGroupCommandValidator;
use TailgateApi\Validators\UpdateEmailCommandValidator;
use TailgateApi\Validators\UpdateGameScoreCommandValidator;
use TailgateApi\Validators\UpdateGroupCommandValidator;
use TailgateApi\Validators\UpdateMemberCommandValidator;
use TailgateApi\Validators\UpdateScoreForGroupCommandValidator;
use TailgateApi\Validators\UpdateSeasonCommandValidator;
use TailgateApi\Validators\UpdateTeamCommandValidator;
use TailgateApi\Validators\UpdateUserCommandValidator;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Verraes\ClassFunctions\ClassFunctions;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        ValidatorInterface::class => function (ContainerInterface $container) {

            return new class($container) implements ValidatorInterface
            {   
                private $container;
                private $validator;
                
                public function __construct($container)
                {
                    $this->container = $container;
                }

                public function assert($command) : bool
                {   
                    // TailgateApi\Validators\RegisterUserCommand turns into TailgateApi\Validators\RegisterUserCommandValidator
                    $classname = 'TailgateApi\Validators\\' .ClassFunctions::short($command) . 'Validator';
                    $this->validator = $this->container->make($classname);
                    return  $this->validator->assert($command);
                }

                public function errors() : array
                {   
                    return $this->validator->errors();
                }
            };
        },

        // validators
        RegisterUserCommandValidator::class => function (ContainerInterface $container) {
            return new RegisterUserCommandValidator($container->get(UserViewRepositoryInterface::class));
        },
        RequestPasswordResetCommandValidator::class => function (ContainerInterface $container) {
            return new RequestPasswordResetCommandValidator($container->get(UserViewRepositoryInterface::class));
        },
        ResetPasswordCommandValidator::class => function (ContainerInterface $container) {
            return new ResetPasswordCommandValidator($container->get(UserViewRepositoryInterface::class));
        },
        ActivateUserCommandValidator::class => function (ContainerInterface $container) {
            return new ActivateUserCommandValidator($container->get(UserViewRepositoryInterface::class));
        },
        UpdateUserCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateUserCommandValidator($container->get(UserViewRepositoryInterface::class));
        },
        UpdateEmailCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateEmailCommandValidator($container->get(UserViewRepositoryInterface::class));
        },


        CreateGroupCommandValidator::class => function (ContainerInterface $container) {
            return new CreateGroupCommandValidator(
                $container->get(UserViewRepositoryInterface::class),
                $container->get(MemberViewRepositoryInterface::class)
            );
        },
        UpdateGroupCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateGroupCommandValidator(
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(UserViewRepositoryInterface::class)
            );
        },
        AddMemberToGroupCommandValidator::class => function (ContainerInterface $container) {
            return new AddMemberToGroupCommandValidator(
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(MemberViewRepositoryInterface::class),
                $container->get(UserViewRepositoryInterface::class)
            );
        },
        UpdateMemberCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateMemberCommandValidator(
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(MemberViewRepositoryInterface::class)
            );
        },
        SubmitScoreForGroupCommandValidator::class => function (ContainerInterface $container) {
            return new SubmitScoreForGroupCommandValidator(
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(PlayerViewRepositoryInterface::class),
                $container->get(GameViewRepositoryInterface::class),
                $container->get(FollowViewRepositoryInterface::class)
            );
        },
        UpdateScoreForGroupCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateScoreForGroupCommandValidator(
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(ScoreViewRepositoryInterface::class),
                $container->get(GameViewRepositoryInterface::class)
            );
        },
        AddPlayerToGroupCommandValidator::class => function (ContainerInterface $container) {
            return new AddPlayerToGroupCommandValidator(
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(MemberViewRepositoryInterface::class),
                $container->get(PlayerViewRepositoryInterface::class)
            );
        },
        

        AddTeamCommandValidator::class => function (ContainerInterface $container) {
            return new AddTeamCommandValidator();
        },
        UpdateTeamCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateTeamCommandValidator($container->get(TeamViewRepositoryInterface::class));
        },
        FollowTeamCommandValidator::class => function (ContainerInterface $container) {
            return new FollowTeamCommandValidator(
                $container->get(TeamViewRepositoryInterface::class),
                $container->get(GroupViewRepositoryInterface::class),
                $container->get(FollowViewRepositoryInterface::class),
                $container->get(SeasonViewRepositoryInterface::class)
            );
        },
        

        CreateSeasonCommandValidator::class => function (ContainerInterface $container) {
            return new CreateSeasonCommandValidator();
        },
        UpdateSeasonCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateSeasonCommandValidator($container->get(SeasonViewRepositoryInterface::class));
        },
        AddGameCommandValidator::class => function (ContainerInterface $container) {
            return new AddGameCommandValidator(
                $container->get(SeasonViewRepositoryInterface::class),
                $container->get(TeamViewRepositoryInterface::class)
            );
        },
        UpdateGameScoreCommandValidator::class => function (ContainerInterface $container) {
            return new UpdateGameScoreCommandValidator(
                $container->get(SeasonViewRepositoryInterface::class),
                $container->get(GameViewRepositoryInterface::class)
            );
        },
    ]);
};