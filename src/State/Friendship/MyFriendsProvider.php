<?php

namespace App\State\Friendship;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Friendship;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;

class MyFriendsProvider implements ProviderInterface
{

    public function __construct(
        private readonly Security $security,
        private readonly DocumentManager $documentManager,
    ){}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $currentUser = $this->security->getUser();
        $repo = $this->documentManager->getRepository(Friendship::class);
        $friendship =  $repo->findOneBy(['user' => $currentUser]);
        if(is_null($friendship)) {
            return [];
        }
        else return $friendship;
    }
}
