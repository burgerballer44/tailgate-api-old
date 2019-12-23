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
    /**
     * view all seasons
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function all(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $seasons = $this->container->get('queryBus')->handle(new AllSeasonsQuery());
        return $this->respondWithData($response, $seasons);
    }

    /**
     * view single season
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $season = $this->container->get('queryBus')->handle(new SeasonQuery($seasonId));
        $season['eventLog'] = $this->container->get('viewRepository.eventLog')->allById($seasonId);
        return $this->respondWithData($response, $season);
    }

    /**
     * create a season
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
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

    /**
     * update a season
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function seasonPatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
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

    /**
     * delete a season
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function seasonDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $season = $this->container->get('queryBus')->handle(new SeasonQuery($seasonId));
        $command = new DeleteSeasonCommand($seasonId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }

    /**
     * add a game to a season
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function gamePost(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $parsedBody = $request->getParsedBody();

        $command = new AddGameCommand(
            $seasonId,
            $parsedBody['homeTeamId'] ?? '',
            $parsedBody['awayTeamId'] ?? '',
            $parsedBody['startDate'] ?? '',
            $parsedBody['startTime'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);
        
        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * change score for a game
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function updateGameScorePatch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $parsedBody = $request->getParsedBody();
        
        $command = new UpdateGameScoreCommand(
            $seasonId,
            $gameId,
            $parsedBody['homeTeamScore'] ?? '',
            $parsedBody['awayTeamScore'] ?? '',
            $parsedBody['startDate'] ?? '',
            $parsedBody['startTime'] ?? ''
        );

        $validator = $this->container->get('validationInflector')->getValidatorClass($command);

        if ($validator->assert($command)) {
            $this->container->get('commandBus')->handle($command);
            return $response;
        }

        return $this->respondWithValidationError($response, $validator->errors());
    }

    /**
     * delete a game
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  [type]                 $args     [description]
     * @return [type]                           [description]
     */
    public function gameDelete(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        extract($args);
        $season = $this->container->get('queryBus')->handle(new SeasonQuery($seasonId));
        $command = new DeleteGameCommand($seasonId, $gameId);
        $this->container->get('commandBus')->handle($command);
        return $response;
    }
}