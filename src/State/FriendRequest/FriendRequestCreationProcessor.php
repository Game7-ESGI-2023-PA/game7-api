<?php

namespace App\State\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Document\FriendRequest;
use App\Exception\FriendRequestException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;

class FriendRequestCreationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security,
        private readonly DocumentManager $documentManager
    ) {
    }

    /**
     * @throws FriendRequestException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();
        $data->setFrom($currentUser);

        if($data->getTo() == $currentUser) {
            throw new FriendRequestException('Cannot send friend request to yourself');
        }

        $repo = $this->documentManager->getRepository(FriendRequest::class);
        if($repo->isFriendRequestExisting($currentUser, $data->getTo())) {
            throw new FriendRequestException('A friend request with this users already exists (accepted or pending)');
        }

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
