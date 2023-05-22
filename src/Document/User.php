<?php

namespace App\Document;

use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use App\State\User\CurrentUserProvider;
use App\State\User\UserPasswordHasher;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Get(
            uriTemplate: 'me',
            provider: CurrentUserProvider::class
        ),
        new GetCollection(),
        new Post(
            uriTemplate: 'register',
            validationContext: ['groups' => ['Default', 'user:create']],
            processor: UserPasswordHasher::class
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:create', 'user:update']]
)]
#[ODM\Document(repositoryClass: UserRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'partial', 'nickname' => 'partial'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['user:read', 'friendRequest:read', 'friendship:read', 'gameLobby:read'])]
    #[ODM\Id]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:read', 'user:create', 'user:update', 'friendRequest:read', 'friendship:read', 'gameLobby:read'])]
    #[ODM\Field]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Groups(['user:read', 'user:create', 'user:update', 'friendRequest:read', 'friendship:read', 'gameLobby:read'])]
    #[ODM\Field]
    private ?string $nickname = null;

    #[ODM\Field]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ODM\Field(type: 'collection')]
    private array $roles = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
