<?php

namespace App\DTO;

readonly class ProjectDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $color = null,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            color: $data['color'] ?? null,
            userId: $data['user_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'user_id' => $this->userId,
        ], fn($value) => $value !== null);
    }
}
