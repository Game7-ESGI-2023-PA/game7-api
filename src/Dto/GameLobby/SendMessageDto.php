<?php

namespace App\Dto\GameLobby;

use Symfony\Component\Serializer\Annotation\Groups;

final class SendMessageDto
{
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
