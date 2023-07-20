<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class GameLobbyCreationProcessor implements ProcessorInterface
{
    public function __construct(
        private Security           $security,
        private ProcessorInterface $processor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();

        $data->setMaster($currentUser);
        $data->addPlayer($currentUser);
        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
