<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\GameLobby;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Symfony\Bundle\SecurityBundle\Security;

readonly class MyGameLobbyProvider implements ProviderInterface {

    public function __construct(
        private Security $security,
        private DocumentManager $documentManager
    )
    {
    }

    /**
     * @throws MappingException
     * @throws LockException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $currentUser = $this->security->getUser();
        $userId = $uriVariables['id'];
        $userRepo = $this->documentManager->getRepository(User::class);
        $repository = $this->documentManager->getRepository(GameLobby::class);
        $user = $userRepo->find($userId);

        if(is_null($user)) {
            return [];
        }

        if ($user->getId() === $currentUser->getUserIdentifier()) {
            return $repository->getAllMyLobbies($user);
        }
        else {
            return $repository->findBy(['master' => $user]);
        }
        // TODO: check if friends
    }

}
