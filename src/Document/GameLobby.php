<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\GameLobby\SendMessageDto;
use App\Exception\GameLobbyException;
use App\State\GameLobby\GameLobbyCreationProcessor;
use App\State\GameLobby\GameLobbyJoinProcessor;
use App\State\GameLobby\GameLobbySendMessageProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

// TODO: change status (only master) -> trigger le moteur de jeux
// TODO: start the game (only master) -> send information to game dispatcher

#[ODM\Document]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(), // TODO: add rbac
        new Post(
            processor: GameLobbyCreationProcessor::class
        ),
        new Put(
            uriTemplate: '/game_lobbies/{id}/join',
            exceptionToStatus: [GameLobbyException::class => 400],
            denormalizationContext: ['groups' => ['gameLobby:join']],
            processor: GameLobbyJoinProcessor::class,
        ),
        new Put(
            uriTemplate: '/game_lobbies/{id}/send_message',
            input: SendMessageDto::class,
            processor: GameLobbySendMessageProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['gameLobby:read']],
    denormalizationContext: ['groups' => ['gameLobby:write']],
    mercure: true
)]
class GameLobby
{
    public const STATUS = ['pending', 'playing', 'done'];

    #[ODM\Id]
    #[Groups(['game:read','gameLobby:read'])]
    private ?string $id = null;

    #[ODM\ReferenceOne(storeAs: 'id', targetDocument: User::class)]
    #[Groups(['game:read', 'gameLobby:read'])]
    private ?User $master = null;

    #[ODM\ReferenceOne(storeAs: 'id', targetDocument: Game::class)]
    #[ApiProperty(
        example: '/api/games/{gameId}',
    )]
    #[Groups(['gameLobby:write', 'gameLobby:read'])]
    private ?Game $game = null;

    #[ODM\ReferenceMany(targetDocument: User::class)]
    #[ApiProperty(
        example: '["/api/users/{userId}", "/api/users/{userId}"]',
    )]
    #[Groups(['game:read', 'gameLobby:read'])]
    private ?ArrayCollection $players = null;

    #[Groups(['gameLobby:read'])]
    #[ODM\EmbedMany(targetDocument: LobbyMessage::class)]
    private ?ArrayCollection $messages = null;

    #[Groups(['game:read', 'gameLobby:read'])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(choices: GameLobby::STATUS, message: 'Invalid status.')]
    private string $status = 'pending';

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function setGame(Game $game): self
    {
        $this->game = $game;
        $this->game->addLobby($this);

        return $this;
    }

    public function getPlayers(): ArrayCollection
    {
        return $this->players;
    }

    public function addPlayer(User $user): self
    {
        if (!$this->players->contains($user)) {
            $this->players[] = $user;
        }

        return $this;
    }

    public function removePlayer(User $user): self
    {
        $this->players->removeElement($user);

        return $this;
    }

    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }

    public function addMessage(LobbyMessage $message): self
    {
        $this->messages[] = $message;
        return $this;
    }

    public function removeMessage(LobbyMessage $message): self
    {
        $this->messages->removeElement($message);
        return $this;
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
