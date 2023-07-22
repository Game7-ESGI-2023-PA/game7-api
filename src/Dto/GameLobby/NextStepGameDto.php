<?php

namespace App\Dto\GameLobby;

use ApiPlatform\Metadata\ApiProperty;
use App\Document\GameLobby;
use Symfony\Component\Serializer\Annotation\Groups;

class NextStepGameDto
{
    #[Groups([GameLobby::INIT_GAME, GameLobby::NEXT_STEP])]
    #[ApiProperty(
        example: "[
            {
              \"actions\": [
                {
                  \"player\": 1,
                  \"x\": 60,
                  \"y\": 60
                }
              ]
            }
        ]"
    )]
    private ?array $args = null;

    /**
     * @return array|null
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * @param array|null $args
     */
    public function setArgs(?array $args): void
    {
        $this->args = $args;
    }
}
