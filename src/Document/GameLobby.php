<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\GameLobbyRepository;
use App\State\GameLobby\GameLobbyCreationProcessor;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ODM\Document(repositoryClass: GameLobbyRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            processor: GameLobbyCreationProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['gameLobby:read']],
    denormalizationContext: ['groups' => ['gameLobby:write']],
)]
class GameLobby
{
    #[ODM\Id]
    #[Groups(['gameLobby:read'])]
    private ?string $id = null;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    #[Groups(['gameLobby:read'])]
    private ?User $master = null;

    #[ODM\Field]
    #[Assert\Type('int')]
    #[Groups(['gameLobby:write', 'gameLobby:read'])]
    private ?int $maxPlayers = null;

    #[ODM\ReferenceOne(targetDocument: Game::class)]
    #[ApiProperty(
        example: '/api/games/{gameId}',
    )]
    #[Groups(['gameLobby:write', 'gameLobby:read'])]
    private ?Game $game = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(?int $maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    public function getMaster(): ?User
    {
        return $this->master;
    }

    public function setMaster(?User $master): void
    {
        $this->master = $master;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): void
    {
        $this->game = $game;
    }
}
