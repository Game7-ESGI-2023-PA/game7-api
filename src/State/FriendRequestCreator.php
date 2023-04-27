<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Document\FriendRequest;
use App\Exception\FriendRequestInvalidException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

class FriendRequestCreator implements ProcessorInterface
{

    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security,
        private readonly DocumentManager $documentManager
    ){}

    /**
     * @throws FriendRequestInvalidException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();
        $status = 'pending';

        $data->setFrom($currentUser);
        $data->setStatus($status);

        if($data->getTo() == $currentUser){
            throw new FriendRequestInvalidException('Cannot send friend request to yourself');
        }

        $repo = $this->documentManager->getRepository(FriendRequest::class);
        if($repo->isFriendRequestExisting($currentUser, $data->getTo())){
            throw new FriendRequestInvalidException('A friend request with this users already exists (accepted or pending)');
        }

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
