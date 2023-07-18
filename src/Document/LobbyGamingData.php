<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ODM\EmbeddedDocument]
class LobbyGamingData
{
    #[Groups([GameLobby::READ, GameLobby::INIT_GAME])]
    #[ODM\field(type: 'hash')]
    private ?array $gameInitArgs;

    #[Groups([GameLobby::READ])]
    #[ODM\field(type: 'hash')]
    private ?array $gameInstructions = null;

    #[Groups([GameLobby::READ])]
    #[ODM\field(type: 'hash')]
    private ?array $gameState;

    public function __construct(?array $gameInitArgs, ?array $gameState)
    {
        $this->gameInitArgs = $gameInitArgs;
        $this->gameState = $gameState;
    }


    public function getGameState(): ?array
    {
        return $this->gameState;
    }

    public function setGameState(?array $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function getGameInitArgs(): ?array
    {
        return $this->gameInitArgs;
    }

    public function setGameInitArgs(?array $gameInitArgs): void
    {
        $this->gameInitArgs = $gameInitArgs;
    }

    public function getGameInstructions(): ?array
    {
        return $this->gameInstructions;
    }

    public function setGameInstructions(?array $gameInstructions): void
    {
        $this->gameInstructions = $gameInstructions;
    }
}
