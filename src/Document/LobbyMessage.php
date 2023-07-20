<?php

namespace App\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ODM\EmbeddedDocument]
class LobbyMessage
{
    #[ODM\Id]
    #[Groups([GameLobby::READ])]
    private ?string $id = null;
    #[ODM\Field]
    #[Groups([GameLobby::READ])]
    private ?string $senderId = null;
    #[ODM\Field]
    #[Groups([GameLobby::READ])]
    private ?string $senderName = null;
    #[ODM\Field]
    #[Groups([GameLobby::READ])]
    private ?\DateTime $dateTime = null;
    #[ODM\Field]
    #[Groups([GameLobby::READ])]
    private ?string $content = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    public function setSenderId(?string $senderId): void
    {
        $this->senderId = $senderId;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
