<?php

namespace TailgateApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Application\Query\Season\AllSeasonsQuery;
use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Application\Command\Season\UpdateSeasonCommand;


class SeasonController extends ApiController
{
    // admin - can view all seasons
    // regular - can view all seasons
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasons = $this->container->get('queryBus')->handle(new AllSeasonsQuery());
       return $this->respondWithData($response, $seasons);
    }

    // admin - can view details of a season
    // regular - can view details of a season
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasonId = $args['seasonId'];
        $season = $this->container->get('queryBus')->handle(new SeasonQuery($seasonId));
        
        return $this->respondWithData($response, $season);
    }

    // admin - can create a season
    public function createPost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $command = new CreateSeasonCommand(
            $parsedBody['name'] ?? '',
            $parsedBody['sport'] ?? '',
            $parsedBody['seasonType'] ?? '',
            $parsedBody['seasonStart'] ?? '',
            $parsedBody['seasonEnd'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
          }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // admin - can add a game to a season
    public function gamePost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasonId = $args['seasonId'];
        $parsedBody = $request->getParsedBody();

        $command = new AddGameCommand(
            $seasonId,
            $parsedBody['homeTeamId'] ?? '',
            $parsedBody['awayTeamId'] ?? '',
            $parsedBody['startDate'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    // admin - can add the final score to a game
    public function updateGameScorePatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasonId = $args['seasonId'];
        $gameId = $args['gameId'];

        $parsedBody = $request->getParsedBody();
        
        $command = new UpdateGameScoreCommand(
            $seasonId,
            $gameId,
            $parsedBody['homeTeamScore'] ?? '',
            $parsedBody['awayTeamScore'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);

        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    //
    public function seasonDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasonId = $args['seasonId'];
        
        $command = new DeleteSeasonCommand($seasonId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    //
    public function gameDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasonId = $args['seasonId'];
        $gameId = $args['gameId'];
        
        $command = new DeleteGameCommand($seasonId, $gameId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    //
    public function seasonPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasonId = $args['seasonId'];
        $parsedBody = $request->getParsedBody();
        
        $command = new UpdateSeasonCommand(
            $seasonId,
            $parsedBody['name'] ?? '',
            $parsedBody['sport'] ?? '',
            $parsedBody['seasonType'] ?? '',
            $parsedBody['seasonStart'] ?? '',
            $parsedBody['seasonEnd'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);

        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }
}