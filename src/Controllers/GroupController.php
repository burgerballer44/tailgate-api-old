<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\QueryGroupsQuery;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;

class GroupController extends ApiController
{
    /**
     * get all group that authenticated user belong to
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $groups = $this->container->get('queryBus')->handle(new AllGroupsQuery($userId));
        return $this->respondWithData($response, $groups);
    }

    /**
     * create a group, assign authenticed user as owner, and member
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function createPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
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

    /**
     * [view description]
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        extract($args);
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        return $this->respondWithData($response, $group);
    }

    /**
     * delete a group
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function groupDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $command = new DeleteGroupCommand($groupId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }





    // admin - can add any user to any group
    // regular - can join a group by invitation code
    // regular Group Admin - can add members to their own group by invitation email that would create a pending user
    public function memberPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $parsedBody = $request->getParsedBody();

        $command = new AddMemberToGroupCommand(
            $groupId,
            $parsedBody['userId'] ?? ''
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
        $memberId = $args['memberId'];
        $parsedBody = $request->getParsedBody();

        $command = new AddPlayerToGroupCommand(
            $groupId,
            $memberId,
            $parsedBody['username'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // admin - can submit a score for anyone
    // regular - can submit a score for themselves
    // regular Group Admin - can submit a score for group members
    public function scorePost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $playerId = $args['playerId'];
        $parsedBody = $request->getParsedBody();

        $command = new SubmitScoreForGroupCommand(
            $groupId,
            $playerId,
            $parsedBody['gameId'],
            $parsedBody['homeTeamPrediction'],
            $parsedBody['awayTeamPrediction']
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // 
    public function memberDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $memberId = $args['memberId'];

        $command = new DeleteMemberCommand($groupId, $memberId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    // 
    public function playerDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $playerId = $args['playerId'];

        $command = new DeletePlayerCommand($groupId, $playerId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    // 
    public function scoreDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $scoreId = $args['scoreId'];

        $command = new DeleteScoreCommand($groupId, $scoreId);
        $this->container->get('commandBus')->handle($command);
        return $response;;
    }

    // 
    public function groupPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $groupId = $args['groupId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdateGroupCommand(
            $groupId,
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

    // 
    public function memberPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groupId = $args['groupId'];
        $memberId = $args['memberId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdateMemberCommand(
            $groupId,
            $memberId,
            $parsedBody['groupRole'],
            $parsedBody['allowMultiple'],
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
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

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }





    // public function query(ServerRequestInterface $request, ResponseInterface $response, $args)
    // {
    //     $params = $request->getQueryParams();
    //     $userId = $params['userId'] ?? '';
    //     $name = $params['name'] ?? '';

    //     $groups = $this->container->get('queryBus')->handle(new QueryGroupsQuery($userId, $name));
    //     return $this->respondWithData($response, $groups);
    // }
}