<?php
namespace App\Message;

class NotificationMessage
{
    public function __construct(
        private string $type,
        private string $label,
        private ?int $userId = null
    ) {}

    public function getType(): string { return $this->type; }
    public function getLabel(): string { return $this->label; }
    public function getUserId(): ?int { return $this->userId; }
}
