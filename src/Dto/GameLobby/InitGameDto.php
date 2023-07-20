<?php

namespace App\Dto\GameLobby;

use ApiPlatform\Metadata\ApiProperty;
use App\Document\GameLobby;
use Symfony\Component\Serializer\Annotation\Groups;

final class InitGameDto
{
    #[Groups(GameLobby::INIT_GAME)]
    #[ApiProperty(
        example: "{ \"arg1\": \"val1\", \"arg1..\": \"val..\"}"
    )]
    public ?array $args = null;

    public function getArgs(): ?array
    {
        return $this->args;
    }

    public function setArgs(?array $args): void
    {
        $this->args = $args;
    }


}
