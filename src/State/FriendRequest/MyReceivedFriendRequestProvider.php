<?php

namespace App\State\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\FriendRequest;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;

class MyReceivedFriendRequestProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly DocumentManager $documentManager
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $currentUser = $this->security->getUser();
        $repo = $this->documentManager->getRepository(FriendRequest::class);
        return $repo->findBy(['to' => $currentUser, 'status' => 'pending']);
    }
}
