<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Tailgate\Application\Query\Team\TeamQuery;
use Tailgate\Application\Query\Team\AllTeamsQuery;
use Tailgate\Application\Command\Team\AddTeamCommand;
use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Command\Team\UpdateTeamCommand;

class TeamController extends ApiController
{
    /**
     * view all teams
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $teams = $this->container->get('queryBus')->handle(new AllTeamsQuery());
        return $this->respondWithData($response, $teams);
    }

    /**
     * view details of a team
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $team = $this->container->get('queryBus')->handle(new TeamQuery($teamId));
        $team['eventLog'] = $this->container->get('viewRepository.eventLog')->allById($teamId);
        return $this->respondWithData($response, $team);
    }

    /**
     * add a team
     * @param ServerRequestInterface $request  [description]
     * @param ResponseInterface      $response [description]
     * @param [type]                 $args     [description]
     */
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

    /**
     * delete a team
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function teamDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $team = $this->container->get('queryBus')->handle(new TeamQuery($teamId));
        $command = new DeleteTeamCommand($teamId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    /**
     * update a team
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function teamPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
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