<?php

namespace App\Controller\FriendRequest;

use App\Document\FriendRequest;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MyReceivedFriendRequest extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly DocumentManager $documentManager
    ){}

    public function __invoke(): array
    {
        $currentUser = $this->security->getUser();
        $repo = $this->documentManager->getRepository(FriendRequest::class);
        return $repo->findBy(['to' => $currentUser->getUserIdentifier()]);
    }
}
