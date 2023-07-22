<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\GameLobby\InitGameDto;
use App\Dto\GameLobby\NextStepGameDto;
use App\Dto\GameLobby\SendMessageDto;
use App\Exception\GameInitException;
use App\Exception\GameLobbyException;
use App\State\GameLobby\Game\InitGameProcessor;
use App\State\GameLobby\Game\GameCurrentStepProcessor;
use App\State\GameLobby\GameLobbyCreationProcessor;
use App\State\GameLobby\GameLobbyJoinProcessor;
use App\State\GameLobby\GameLobbySendMessageProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

// TODO: start the game (only master) -> send information to game dispatcher

#[ODM\Document]
#[ApiResource(
    operations: [
        new Get(), // TODO: add rbac using voters
        new Post(
            processor: GameLobbyCreationProcessor::class
        ),
        new Put(
            uriTemplate: '/game_lobbies/{id}/join',
            exceptionToStatus: [GameLobbyException::class => 400],
            denormalizationContext: ['groups' => [self::JOIN]],
            processor: GameLobbyJoinProcessor::class,
        ),
        new Put(
            uriTemplate: '/game_lobbies/{id}/send_message',
            denormalizationContext: ['groups' => [self::WRITE_MESSAGE]],
            input: SendMessageDto::class,
            processor: GameLobbySendMessageProcessor::class,
        ),
        new Put(
            // TODO: only master
            uriTemplate: '/game_lobbies/{id}/init_game',
            exceptionToStatus: [GameLobbyException::class => 400, GameInitException::class => 400],
            denormalizationContext: ['groups' => [self::INIT_GAME]],
            input: InitGameDto::class,
            processor: InitGameProcessor::class,
        ),
        new Put(
            // TODO: only master
            uriTemplate: '/game_lobbies/{id}/next_step',
            exceptionToStatus: [GameLobbyException::class => 400, GameInitException::class => 400],
            denormalizationContext: ['groups' => [self::NEXT_STEP]],
            input: NextStepGameDto::class,
            processor: GameCurrentStepProcessor::class,
        )
    ],
    normalizationContext: ['groups' => [self::READ]],
    denormalizationContext: ['groups' => [self::CREATE]],
    mercure: true // TODO: only players can subscribe
)]
class GameLobby
{
    public const STATUS = ['pending', 'playing', 'done'];
    public const READ = 'gameLobby:read';
    public const CREATE = 'gameLobby:create';
    public const WRITE_MESSAGE = 'gameLobby:write:message';
    public const JOIN = 'gameLobby:join';
    public const INIT_GAME = 'gameLobby:init:game';
    public const NEXT_STEP = 'gameLobby:next:step';

    #[ODM\Id]
    #[Groups([Game::READ, self::READ])]
    private ?string $id = null;

    #[ODM\ReferenceOne(storeAs: 'id', targetDocument: User::class)]
    #[Groups([Game::READ, self::READ])]
    private ?User $master = null;

    #[ODM\ReferenceOne(storeAs: 'id', targetDocument: Game::class)]
    #[ApiProperty(
        example: '/api/games/{gameId}',
    )]
    #[Groups([self::CREATE, self::READ])]
    private ?Game $game = null;

    #[ODM\ReferenceMany(targetDocument: User::class)]
    #[ApiProperty(
        example: '["/api/users/{userId}", "/api/users/{userId}"]',
    )]
    #[Groups([Game::READ, self::READ])]
    private ?ArrayCollection $players = null;

    #[Groups([self::READ])]
    #[ODM\EmbedMany(targetDocument: LobbyMessage::class)]
    private ?ArrayCollection $messages = null;

    #[Groups([Game::READ, self::READ])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(choices: GameLobby::STATUS, message: 'Invalid status.')]
    private string $status = 'pending';

    #[Groups([self::READ])]
    #[ODM\EmbedOne(targetDocument: LobbyGamingData::class)]
    private ?LobbyGamingData $lobbyGamingData = null;

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

    public function getLobbyGamingData(): ?LobbyGamingData
    {
        return $this->lobbyGamingData;
    }

    public function setLobbyGamingData(?LobbyGamingData $lobbyGamingData): void
    {
        $this->lobbyGamingData = $lobbyGamingData;
    }
}
