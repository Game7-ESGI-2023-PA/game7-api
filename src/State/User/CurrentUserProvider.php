<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserProvider implements ProviderInterface
{

    public function __construct(
        private readonly Security $security,
        private readonly DocumentManager $documentManager
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $currentUser = $this->security->getUser();
        $repo = $this->documentManager->getRepository(User::class);
        return $repo->findOneBy(['id' => $currentUser->getUserIdentifier()]);
    }
}
