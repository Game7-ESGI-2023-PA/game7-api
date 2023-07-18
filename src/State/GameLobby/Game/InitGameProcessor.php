<?php

namespace App\State\GameLobby\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Document\GameLobby;
use App\Document\LobbyGamingData;
use App\Exception\GameInitException;
use App\Exception\GameLobbyException;
use App\Service\QueryGameDispatcher;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class InitGameProcessor implements ProcessorInterface
{

    public function __construct(
        private DocumentManager     $documentManager,
        private QueryGameDispatcher $gameDispatcher,
        private ProcessorInterface  $processor,
    ){}

    /**
     * @throws GameLobbyException
     * @throws GameInitException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $lobbyId = $uriVariables['id'];
        $gameArgs = $data->getArgs();
        $repository = $this->documentManager->getRepository(GameLobby::class);

        /* @var GameLobby $lobby*/
        $lobby = $repository->findOneBy(['id' => $lobbyId]);
        if (is_null($lobby)) {
            throw new GameLobbyException("Game lobby not found ".$lobbyId);
        }

        if (!is_null($lobby->getLobbyGamingData())) {
            throw new GameLobbyException("Game already started ".$lobbyId);
        }

        try {
            $gameState = $this->gameDispatcher->queryGameEngine(
                $gameArgs,
                $lobby->getGame()->getDirName(),
                $lobby->getGame()->getExecutableName(),
                []
            );

            if (!is_null($gameState)) {
                $lobby->setLobbyGamingData(new LobbyGamingData($gameArgs, [$gameState]));
                $lobby->setStatus('playing');
            }
            return $this->processor->process($lobby, $operation, $uriVariables, $context);
        } catch (Exception|DecodingExceptionInterface|TransportExceptionInterface $e) {
            throw new GameInitException("An error occurred while starting the game :".$e->getMessage());
        }
    }
}
