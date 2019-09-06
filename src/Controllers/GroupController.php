<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;

class GroupController extends ApiController
{
    // admin - gets all groups
    // regular - get all groups they belong to
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groups = $this->container->get('queryBus')->handle(new AllGroupsQuery());
        return $this->respondWithData($response, $groups);
    }

    // admin - view any group
    // regular - view group they belong to
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $group = $this->container->get('queryBus')->handle(new GroupQuery($groupId));
       
        return $this->respondWithData($response, $group);
    }

    // admin - can create any number of groups and assign the owner
    // regular - can create a group that they have to be the owner of
    public function createPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('user_id');
        $parsedBody = $request->getParsedBody();

        $command = new CreateGroupCommand(
            $parsedBody['name'],
            $userId
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // admin - can add any user to any group
    // regular - can join a group by invitation code
    // regular Group Admin - can add members to their own group by invitation email that would create a pending user
    public function memberPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $parsedBody = $request->getParsedBody();

        $command = new AddMemberToGroupCommand(
            $group_id,
            $parsedBody['user_id'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    public function playerPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $parsedBody = $request->getParsedBody();

        $command = new AddPlayerToGroupCommand(
            $group_id,
            $parsedBody['member_id'] ?? '',
            $parsedBody['username'] ?? ''
        );

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        // return $this->respondWithValidationError($response, $validator->errors());
    }

    // admin - can submit a score for anyone
    // regular - can submit a score for themselves
    // regular Group Admin - can submit a score for group members
    public function scorePost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $userId = $request->getAttribute('user_id');
        $parsedBody = $request->getParsedBody();

        $command = new SubmitScoreForGroupCommand(
            $groupId,
            $userId,
            $parsedBody['game_id'],
            $parsedBody['home_team_prediction'],
            $parsedBody['away_team_prediction']
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function groupDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];

        $command = new DeleteGroupCommand($groupId);

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        // }

        // return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function memberDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $memberId = $args['memberId'];

        $command = new DeleteMemberCommand($groupId, $memberId);

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        // }

        // return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function scoreDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $scoreId = $args['scoreId'];

        $command = new DeleteScoreCommand($groupId, $scoreId);

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        // }

        // return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function groupPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $request->getAttribute('groupId');
        $parsedBody = $request->getParsedBody();

        $command = new UpdateGroupCommand(
            $groupId,
            $parsedBody['name'],
            $parsedBody['userId']
        );

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        // }

        // return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function memberPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $request->getAttribute('groupId');
        $memberId = $request->getAttribute('memberId');
        $parsedBody = $request->getParsedBody();

        $command = new UpdateMemberCommand(
            $groupId,
            $memberId,
            $parsedBody['groupRole']
        );

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        // }

        // return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function scorePatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $scoreId = $args['scoreId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdateScoreForGroupCommand(
            $groupId,
            $scoreId,
            $parsedBody['homeTeamPrediction'],
            $parsedBody['awayTeamPrediction']
        );

        // $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        // if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        // }

        // return $this->respondWithValidationError($response, $validator->errors());
    }
}