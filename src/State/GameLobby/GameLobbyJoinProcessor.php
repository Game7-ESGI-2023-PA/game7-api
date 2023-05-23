<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\GameLobbyException;
use Symfony\Bundle\SecurityBundle\Security;

class GameLobbyJoinProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly Security $security,
        private readonly ProcessorInterface $processor
    )
    {}

    /**
     * @throws GameLobbyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();
        if(count($data->getPlayers()) >= $data->getMaxPlayers()) {
            throw new GameLobbyException('Maximum player number is already reached');
        }
        $data->addPlayers($currentUser);
        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
