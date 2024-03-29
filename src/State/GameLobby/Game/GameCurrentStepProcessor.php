<?php

namespace App\State\GameLobby\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\QueryGameDispatcher;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Exception\GameLobbyException;
use App\Document\GameLobby;
use Exception;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GameCurrentStepProcessor implements ProcessorInterface
{

    public function __construct(
        private DocumentManager     $documentManager,
        private QueryGameDispatcher $gameDispatcher,
        private ProcessorInterface  $processor,
    ){}

    /**
     * @throws GameLobbyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $lobbyId = $uriVariables['id'];
        $newActions = $data->getArgs();
        $repository = $this->documentManager->getRepository(GameLobby::class);

        /* @var GameLobby $lobby*/
        $lobby = $repository->findOneBy(['id' => $lobbyId]);
        if (is_null($lobby)) {
            throw new GameLobbyException("Game lobby not found ".$lobbyId);
        }
        if (is_null($lobby->getLobbyGamingData())) {
            throw new GameLobbyException("Game not started ".$lobbyId);
        }

        $actions = $lobby->getLobbyGamingData()->getGameInstructions();
        if (is_null($actions)) {
            $sendActions = [ $newActions ];
        }
        else {
            $actions[] = $newActions;
            $sendActions = $actions;
        }
        try {
            $gameState = $this->gameDispatcher->queryGameEngine(
                $lobby->getLobbyGamingData()->getGameInitArgs(),
                $lobby->getGame()->getDirName(),
                $lobby->getGame()->getExecutableName(),
                $sendActions
            );
            $lobby->getLobbyGamingData()->addGameInstructions($newActions);
            $lobby->getLobbyGamingData()->addGameState($gameState);
            if (isset($gameState['game_state']['game_over']) && $gameState['game_state']['game_over'] === true) {
                $lobby->setStatus('over');
                if (is_array($gameState['game_state']['scores'])) {
                    $max = max($gameState['game_state']['scores']);
                    $maxIndex = array_keys($gameState['game_state']['scores'], $max);
                    foreach ($maxIndex as $index) {
                        $player = $lobby->getPlayers()->get($index);
                        $player->addXp($lobby->getGame()->getWinXp());
                        $this->documentManager->persist($player);
                    }
                    $this->documentManager->flush();
                }
            }
            return $this->processor->process($lobby, $operation, $uriVariables, $context);
        } catch (Exception|DecodingExceptionInterface|TransportExceptionInterface $e) {
            throw new GameLobbyException("An error occurred while starting the game :".$e->getMessage());
        }
    }
}
