<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\FriendRequestException;
use Symfony\Bundle\SecurityBundle\Security;

class FriendRequestAnswer implements ProcessorInterface
{

    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security,
    ){}

    /**
     * @throws FriendRequestException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $sentStatus = $data->getStatus();
        $currentUser = $this->security->getUser();
        if($data->getTo() != $currentUser ) {
            throw new FriendRequestException('the answerer needs to be the receiver of the request');
        }

        # TODO: can't change status if actual already accepted or refused

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
