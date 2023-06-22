<?php

namespace App\State\GameLobby;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Document\GameLobby;
use App\Document\LobbyMessage;
use App\Document\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;

readonly class GameLobbySendMessageProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly DocumentManager $documentManager,
        private readonly Security $security,
        private readonly ProcessorInterface $processor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $lobbyId = $uriVariables['id'];
        $repository = $this->documentManager->getRepository(GameLobby::class);
        /* @var GameLobby $lobby*/
        $lobby = $repository->findOneBy(['id' => $lobbyId]);
        if (is_null($lobby)) {
            dd("not found");
           // TODO: throw 404
        }
        if (!$this->isAuthorized($lobby->getPlayers())) {
            dd("unauthorized");
            // TODO: throw 403
        }

        $user= $this->documentManager->find(User::class, $this->security->getUser()->getUserIdentifier());
        $message = new LobbyMessage();
        $message->setContent($data->getMessage());
        $message->setSenderId($user->getId());
        $message->setSenderName($user->getNickname());
        $message->setDateTime(new \DateTime());
        $lobby->addMessage($message);
        return $this->processor->process($lobby, $operation, $uriVariables, $context);
    }

    private function isAuthorized(ArrayCollection $members): bool {
        $currentUser = $this->security->getUser();
        foreach ($members as $member) {
            if($member == $currentUser) {
                return true;
            }
        }
        return false;
    }
}
