<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\FriendRequest\MyReceivedFriendRequest;
use App\Controller\FriendRequest\MySentFriendRequest;
use App\Exception\FriendRequestException;
use App\Repository\FriendRequestRepository;
use App\State\FriendRequestAnswer;
use App\State\FriendRequestCreator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: 'me/received/friend_requests/',
            controller: MyReceivedFriendRequest::class,
            paginationEnabled: false
        ),
        new GetCollection(
            uriTemplate: 'me/sent/friend_requests/',
            controller: MySentFriendRequest::class,
            paginationEnabled: false
        ),
        new Post(
            exceptionToStatus: [FriendRequestException::class => 400],
            processor: FriendRequestCreator::class
        ),
        new Put(
            uriTemplate: '/friend_requests/{id}/answer/',
            denormalizationContext: ['groups' => 'friendRequest:answer'],
            processor: FriendRequestAnswer::class
        )
    ],
    normalizationContext: ['groups' => ['friendRequest:read']],
    denormalizationContext: ['groups' => ['friendRequest:write']]
)]
#[ODM\Document(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[ODM\Id]
    #[Groups(['friendRequest:read'])]
    private ?string $id = null;

    const STATUS = ['pending', 'accepted', 'refused'];

    #[Groups(['friendRequest:read'])]
    #[ApiProperty(
        example: '/api/users/{userId}',
    )]
    #[ODM\ReferenceOne(targetDocument: User::class)]
    private User $from;

    #[Groups(['friendRequest:read', 'friendRequest:write'])]
    #[ODM\ReferenceOne(targetDocument: User::class)]
    #[ApiProperty(
        example: '/api/users/{userId}',
    )]
    private User $to;

    #[Groups(['friendRequest:read', 'friendRequest:answer'])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(choices: FriendRequest::STATUS, message: 'Invalid status.')]
    private string $status = 'pending';

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFrom(): User
    {
        return $this->from;
    }

    public function setFrom(User $from): void
    {
        $this->from = $from;
    }

    public function getTo(): User
    {
        return $this->to;
    }

    public function setTo(User $to): void
    {
        $this->to = $to;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

}
