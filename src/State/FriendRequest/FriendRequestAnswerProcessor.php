<?php

namespace App\State\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Document\Friendship;
use App\Document\User;
use App\Exception\FriendRequestException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\SecurityBundle\Security;

class FriendRequestAnswerProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security,
        private readonly DocumentManager $documentManager,
    ) {
    }

    /**
     * @throws FriendRequestException
     * @throws MongoDBException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();
        if($data->getTo() != $currentUser) {
            throw new FriendRequestException('the answerer needs to be the receiver of the request');
        }
        if($data->getStatus() == 'accepted') {
            $this->addFriend($data->getTo(), $data->getFrom());
            $this->addFriend($data->getFrom(), $data->getTo());
        }

        $this->processor->process($data, $operation, $uriVariables, $context);
    }

    /**
     * @throws MongoDBException
     */
    private function addFriend(User $user, User $friend): void
    {
        $friendshipRepository = $this->documentManager->getRepository(Friendship::class);
        $friendship = $friendshipRepository->findOneBy(['user' => $user]);
        if(is_null($friendship)) {
            $friendship = new Friendship();
            $friendship->setUser($user);
        }
        $friendship->addFriends($friend);
        $this->documentManager->persist($friendship);
        $this->documentManager->flush();
    }
}
