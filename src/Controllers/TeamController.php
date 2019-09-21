<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Command\Team\DeleteFollowCommand;
use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Command\Team\FollowTeamCommand;
use Tailgate\Application\Command\Team\UpdateTeamCommand;

class TeamController extends ApiController
{
    // admin - can view all teams
    // regular - can view all teams
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $teams = $this->container->get('queryBus')->handle(new AllTeamsQuery());
        return $this->respondWithData($response, $teams);
    }

    // admin - can view details of a team
    // regular - can view details of a team
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $teamId = $args['teamId'];
        $team = $this->container->get('queryBus')->handle(new TeamQuery($teamId));
        
        return $this->respondWithData($response, $team);
    }

    // admin - can add a team
    public function addPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $command = new AddTeamCommand(
            $parsedBody['designation'] ?? '',
            $parsedBody['mascot'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // admin - can have group follow any team
    // regular Group Admin - can have their own group follow any team
    public function followPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {   
        $teamId = $args['teamId'];
        $parsedBody = $request->getParsedBody();

        $command = new FollowTeamCommand(
            $parsedBody['groupId'] ?? '',
            $teamId
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    //
    public function teamDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $teamId = $args['teamId'];

        $command = new DeleteTeamCommand($teamId);

        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    //
    public function followDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $teamId = $args['teamId'];
        $followId = $args['followId'];

        $command = new DeleteFollowCommand($teamId, $followId);

        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    //
    public function teamPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $teamId = $args['teamId'];
        $parsedBody = $request->getParsedBody();

        $command = new UpdateTeamCommand(
            $teamId,
            $parsedBody['designation'] ?? '',
            $parsedBody['mascot'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }
}