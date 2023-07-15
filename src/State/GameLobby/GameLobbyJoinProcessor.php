<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\GameLobbyException;
use Symfony\Bundle\SecurityBundle\Security;

readonly class GameLobbyJoinProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private ProcessorInterface $processor
    ) {
    }

    /**
     * @throws GameLobbyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();
        if(count($data->getPlayers()) >= $data->getGame()->getMaxPlayers()) {
            throw new GameLobbyException('Maximum player number is already reached');
        }
        $data->addPlayer($currentUser);
        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
