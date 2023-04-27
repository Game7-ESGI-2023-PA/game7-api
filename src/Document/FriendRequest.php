<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\FriendRequest\MyReceivedFriendRequest;
use App\Repository\FriendRequestRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/friend_requests/my_received',
            controller: MyReceivedFriendRequest::class,
            paginationEnabled: false,
        ),

    ],
    normalizationContext: ['groups' => ['friendRequest:read', 'friendRequest:read']],
    denormalizationContext: ['groups' => ['friendRequest:write']]
)]
#[ODM\Document(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[ODM\Id]
    private ?string $id = null;

    #[Groups(['friendRequest:read', 'friendRequest:write'])]
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

    #[Groups(['friendRequest:read', 'friendRequest:write'])]
    #[ODM\Field(type: 'string')]
    private string $status;

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