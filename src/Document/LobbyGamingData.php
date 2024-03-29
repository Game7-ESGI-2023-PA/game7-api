<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ODM\EmbeddedDocument]
class LobbyGamingData
{
    #[Groups([GameLobby::READ, GameLobby::INIT_GAME])]
    #[ODM\field(type: 'hash')]
    private array $gameInitArgs = [];

    #[Groups([GameLobby::READ])]
    #[ODM\field(type: 'hash')]
    private array $gameInstructions = [];

    #[Groups([GameLobby::READ])]
    #[ODM\field(type: 'hash')]
    private array $gameState = [];


    public function getGameState(): array
    {
        return $this->gameState;
    }

    public function setGameState(array $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function addGameState(array $state): self {
        $this->gameState[] = $state;
        return $this;
    }

    public function getGameInitArgs(): array
    {
        return $this->gameInitArgs;
    }

    public function setGameInitArgs(array $gameInitArgs): void
    {
        $this->gameInitArgs = $gameInitArgs;
    }

    public function getGameInstructions(): array
    {
        return $this->gameInstructions;
    }

    public function setGameInstructions(array $gameInstructions): void
    {
        $this->gameInstructions = $gameInstructions;
    }

    public function addGameInstructions(array $instructions): self {
        $this->gameInstructions[] = $instructions;
        return $this;
    }
}
