<?php

namespace App\Document;

use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

// TODO: only admin can create, update and delete game
// TODO: recherche full text mongo atlas

#[ODM\Document(repositoryClass: GameRepository::class)]
#[ApiResource(
    operations: [
    new Get(),
    new GetCollection(
        normalizationContext: ['groups' => [self::COLLECTION_READ]]
    ),
    new Put(),
    new Delete(),
    new Post()
    ],
    normalizationContext: ['groups' => [self::READ]],
    denormalizationContext: ['groups' => [self::WRITE]],
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial', 'description' => 'partial'])]
class Game
{

    public const READ = 'game:read';
    public const COLLECTION_READ = 'game:collection:read';
    public const WRITE = 'game:write';

    #[ODM\Id]
    #[Groups([self::COLLECTION_READ, self::READ, GameLobby::READ])]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Groups([self::COLLECTION_READ, self::READ, self::WRITE, GameLobby::READ])]
    #[ODM\Index(unique: true)]
    private string $name;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Groups([self::COLLECTION_READ, self::READ, self::WRITE, GameLobby::READ])]
    private string $description;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Groups([self::COLLECTION_READ, self::READ, self::WRITE, GameLobby::READ])]
    private string $dirName;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Groups([self::COLLECTION_READ, self::READ, self::WRITE, GameLobby::READ])]
    private string $executableName;

    #[ODM\Field(type: 'string')]
    #[Assert\Url]
    #[Groups([self::COLLECTION_READ, self::READ, self::WRITE, GameLobby::READ])]
    private string $imageUrl;

    #[ODM\Field(type: 'string')]
    #[Assert\Url]
    #[Groups([self::COLLECTION_READ, self::READ, self::WRITE])]
    private string $bgUrl;

    #[ODM\Field(type: 'int')]
    #[Assert\Positive]
    #[Groups([self::READ, self::WRITE, GameLobby::READ])]
    private int $maxPlayers;

    #[ODM\Field(type: 'int')]
    #[Assert\Positive]
    #[Groups([self::READ, self::WRITE, GameLobby::READ])]
    private int $minPlayers;

    #[ODM\Field(type: 'hash')]
    #[Groups([self::READ, self::WRITE])]
    private ?array $args = null;

    #[Groups([self::READ, self::WRITE])]
    #[ODM\ReferenceMany(storeAs: 'id', targetDocument: GameLobby::class)]
    private ArrayCollection $lobbies;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDirName(): string
    {
        return $this->dirName;
    }

    public function setDirName(string $dirName): void
    {
        $this->dirName = $dirName;
    }

    public function getExecutableName(): string
    {
        return $this->executableName;
    }

    public function setExecutableName(string $executableName): void
    {
        $this->executableName = $executableName;
    }

    public function getBgUrl(): string
    {
        return $this->bgUrl;
    }

    public function setBgUrl(string $bgUrl): void
    {
        $this->bgUrl = $bgUrl;
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

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    public function getMinPlayers(): int
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(int $minPlayers): void
    {
        $this->minPlayers = $minPlayers;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    public function getLobbies(): ArrayCollection
    {
        return $this->lobbies;
    }

    public function addLobby(GameLobby $lobby): self
    {
        if (!$this->lobbies->contains($lobby)) {
            $this->lobbies[] = $lobby;
        }

        return $this;
    }

    public function removeLobby(GameLobby $lobby): self
    {
        $this->lobbies->removeElement($lobby);

        return $this;
    }
}
