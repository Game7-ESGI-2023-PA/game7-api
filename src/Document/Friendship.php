<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\FriendshipRepository;
use App\State\Friendship\MyFriendsProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ODM\Document(repositoryClass: FriendshipRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: 'me/friendship',
            provider: MyFriendsProvider::class
        ),
    ],
    normalizationContext: ['groups' => self::READ]
)]
class Friendship
{
    public const READ = 'friendship:read';

    #[ODM\Id]
    private ?string $id = null;
    #[ODM\ReferenceOne(targetDocument: User::class)]
    #[ODM\Index(unique: true)]
    private ?User $user = null;
    #[Groups(self::READ)]
    #[ODM\ReferenceMany(targetDocument: User::class)]
    private ArrayCollection $friends;

    public function __construct()
    {
        $this->friends = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getFriends(): ArrayCollection
    {
        return $this->friends;
    }

    public function addFriends(User $user): self
    {
        if (!$this->friends->contains($user)) {
            $this->friends[] = $user;
        }

        return $this;
    }

    public function removeFriends(User $user): self
    {
        $this->friends->removeElement($user);

        return $this;
    }

}
