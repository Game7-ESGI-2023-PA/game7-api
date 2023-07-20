<?php

namespace App\Dto\GameLobby;

use App\Document\GameLobby;
use Symfony\Component\Serializer\Annotation\Groups;

final class SendMessageDto
{
    #[Groups(GameLobby::WRITE_MESSAGE)]
    public ?string $message = null;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}
