<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\GameRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(repositoryClass: GameRepository::class)]
#[ApiResource(
    operations: [
    new Get(),
    new GetCollection(),
    new Put(),
    new Delete(),
    new Post()
],
    normalizationContext: ['groups' => ['game:read']],
    denormalizationContext: ['groups' => ['game:write']]
)]
class Game
{
    #[ODM\Id]
    #[Groups(['game:read', 'gameLobby:read'])]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Groups(['game:read', 'game:write', 'gameLobby:read'])]
    private string $name;

    #[ODM\Field(type: 'string')]
    #[Assert\Url]
    #[Groups(['game:read', 'game:write', 'gameLobby:read'])]
    private string $imageUrl;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
