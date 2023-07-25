<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\GameLobbyException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;

readonly class GameLobbyEndProcessor implements ProcessorInterface
{

    public function __construct(private ProcessorInterface $processor, private DocumentManager $documentManager) {}

    /**
     * @throws MongoDBException
     * @throws GameLobbyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if( $data->getStatus() !== 'over') {
            $winner = $data->getWinner();
            $xpToAdd = $data->getGame()->getWinXp();
            $winner->addXp($xpToAdd);
            $data->setStatus('over');
            $this->processor->process($data, $operation, $uriVariables, $context);
        }
        else {
            throw new GameLobbyException("Le jeu est déjà terminer");
        }
    }
}
