<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Tailgate\Application\Query\Group\GroupQuery;
use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Application\Query\Group\GroupInviteCodeQuery;
use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Application\Query\Group\AllGroupsByUserQuery;
use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;

class GroupController extends ApiController
{
    /**
     * join a group by invite code
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function inviteCodePost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $parsedBody = $request->getParsedBody();
        $inviteCode = $parsedBody['inviteCode'];

        // get groupId by invite code
        $group = $this->container->get('queryBus')->handle(new GroupInviteCodeQuery($inviteCode));
        $groupId = $group['groupId'];

        $command = new AddMemberToGroupCommand($groupId, $userId);
        
        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

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
        $groups = $this->container->get('queryBus')->handle(new AllGroupsByUserQuery($userId));
        return $this->respondWithData($response, $groups);
    }

    /**
     * create a group, assign authenticed user as owner if not admin, and member
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function createPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $command = new CreateGroupCommand($parsedBody['name'], $parsedBody['userId']);

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * view a group the user belongs to
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        extract($args);
        $group = $this->container->get('queryBus')->handle(new GroupByUserQuery($userId, $groupId));
        return $this->respondWithData($response, $group);
    }

    /**
     * delete a group the user owns
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function groupDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        extract($args);
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));

        if ($userId != $group['ownerId']) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new DeleteGroupCommand($groupId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    /**
     * add a player to group
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function playerPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        extract($args);
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // if the user is not an admin or the user changing themself
        if ('Group-Admin' != $member['role'] && $member['memberId'] != $memberId) {
            throw new \Exception("Hey! Invalid permissions!");
        }

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

    /**
     * delete a player from a group
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function playerDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        extract($args);
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $playerIds = collect($group['players'])->where('memberId', $member['memberId'])->pluck('playerId')->toArray();

        // if the user is not an admin or the user changing themself
        if ('Group-Admin' != $member['role'] && !in_array($playerId, $playerIds)) {
            throw new \Exception("Hey! Invalid permissions!");
        }
        
        $command = new DeletePlayerCommand($groupId, $playerId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    /**
     * group follows a team
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function followPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {   
        $userId = $request->getAttribute('userId');
        $user = $request->getAttribute('user');

        extract($args);
        $parsedBody = $request->getParsedBody();
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // if the user is not an admin
        if ('Group-Admin' != $member['role'] && 'Admin' != $user['role']) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new FollowTeamCommand(
            $groupId,
            $parsedBody['teamId'] ?? '',
            $parsedBody['seasonId'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * delete a follow
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function followDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $user = $request->getAttribute('user');
        extract($args);
        $parsedBody = $request->getParsedBody();
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);

        // if the user is not an admin
        if ('Group-Admin' != $member['role'] && 'Admin' != $user['role']) {
            throw new \Exception("Hey! Invalid permissions!");
        }
        
        $command = new DeleteFollowCommand($groupId, $followId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    /**
     * submit a score for a game in a group by a player
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function scorePost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $user = $request->getAttribute('user');
        extract($args);
        $parsedBody = $request->getParsedBody();
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $playersIds = collect($group['players'])->where('memberId', $member['memberId'])->pluck('playerId');

        // must be admin, group admin, or thw owner of player
        if ('Group-Admin' != $member['role'] && 'Admin' != $user['role'] && !$playersIds->contains($playerId)) {
            throw new \Exception("Hey! Invalid permissions!");
        }

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

    /**
     * update a score
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function scorePatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $user = $request->getAttribute('user');
        extract($args);
        $parsedBody = $request->getParsedBody();
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $scoreIds = collect($group['scores'])->where('memberId', $member['memberId'])->pluck('scoreId');

        // must be admin, group admin, or thw owner of score
        if ('Group-Admin' != $member['role'] && 'Admin' != $user['role'] && !$scoreIds->contains($scoreId)) {
            throw new \Exception("Hey! Invalid permissions!");
        }

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

    /**
     * delete a score
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function scoreDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $userId = $request->getAttribute('userId');
        $user = $request->getAttribute('user');
        extract($args);
        $parsedBody = $request->getParsedBody();
        $group = $this->container->get('queryBus')->handle(new GroupQuery($userId, $groupId));
        $member = collect($group['members'])->firstWhere('userId', $userId);
        $scoreIds = collect($group['scores'])->where('memberId', $member['memberId'])->pluck('scoreId');

        // must be admin, group admin, or thw owner of score
        if ('Group-Admin' != $member['role'] && 'Admin' != $user['role'] && !$scoreIds->contains($scoreId)) {
            throw new \Exception("Hey! Invalid permissions!");
        }

        $command = new DeleteScoreCommand($groupId, $scoreId);
        $this->container->get('commandBus')->handle($command);
        return $response;;
    }

    /**
     * query groups for admin
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function adminAll(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $groups = $this->container->get('queryBus')->handle(new AllGroupsQuery());
        return $this->respondWithData($response, $groups);
    }

    /**
     * view a group for admin
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function adminView(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $groups = $this->container->get('queryBus')->handle(new GroupQuery($groupId));
        return $this->respondWithData($response, $groups);
    }

    /**
     * update a group
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function adminGroupPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $parsedBody = $request->getParsedBody();

        $command = new UpdateGroupCommand(
            $groupId,
            $parsedBody['name'],
            $parsedBody['ownerId']
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

}