<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\GameLobby;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;

readonly class MyGameLobbyProvider implements ProviderInterface {

    public function __construct(
        private Security        $security,
        private DocumentManager $documentManager
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $currentUser = $this->security->getUser();
        $repository = $this->documentManager->getRepository(GameLobby::class);
        return $repository->getAllMyLobbies($currentUser->getUserIdentifier());
    }

}
